<?php
namespace App\Http\Controllers\Customer;

use App\Enums\ClaimSource;
use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function __construct(protected ClaimService $claimService)
    {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_id'  => 'required',
            'claim_type' => 'required|string',
            'form_data'  => 'required|array',
        ]);

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please log in again.'], 401);
        }

        $policy = Policy::where('customer_id', $customer->id)
            ->where(function ($q) use ($validated) {
                $q->where('external_policy_id', $validated['policy_id'])
                    ->orWhere('id', $validated['policy_id']);
            })
            ->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found. Please go back and select your policy again.'], 404);
        }

        // if ($policy->customer_id !== $customer->id) {
        //     return response()->json(['success' => false, 'message' => 'This policy does not belong to your account.'], 403);
        // }

        $riskId   = $request->input('risk_id') ? (int) $request->input('risk_id') : null;
        $formData = $validated['form_data'];

        if ($riskId) {
            $formData['_risk_id'] = $riskId;
        }

        $claim = $this->claimService->register(
            customer: $customer,
            policy: $policy,
            claimType: $validated['claim_type'],
            formData: $formData,
            source: ClaimSource::CUSTOMER_PORTAL,
            riskId: $riskId,
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Your claim has been submitted successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('claims.index'),
        ]);
    }

    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $claims = Claim::where('customer_id', $customer->id)
            ->with(['policy'])
            ->latest()
            ->paginate(5);

        return view('customer.claims.index', compact('claims', 'customer'));
    }

    public function show(Claim $claim)
    {
        $claim->load(['policy', 'activities.user', 'documents']);
        return view('customer.claims.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        $claim->load(['policy', 'activities.user', 'documents', 'customer']);

        // Load the customer Data for display
        $customer = Customer::find($claim->policy->customer_id);

        // Only allow editing if claim is still in a state where edits make sense
        $editableStatuses = ['submitted', 'pending_info'];

        if (! in_array($claim->status, $editableStatuses)) {
            return redirect()->route('claims.show', $claim)
                ->with('error', 'This claim can no longer be edited.');
        }

        $claim->load(['policy', 'documents']);

        // Map claim_type to the correct edit view — mirrors processClaim() in dashboard JS. Routes commented out becasue they aren't available yet.
        $viewMap = [
            'motor'            => 'customer.claims.edit.motor',
            'fire'             => 'customer.claims.edit.fire',
            'general_accident' => 'customer.claims.edit.general-accident',
            // 'marine'           => 'customer.claims.edit.marine',
            // 'aviation'         => 'customer.claims.edit.aviation',
            // 'bond'             => 'customer.claims.edit.bond',
            // 'engineering'      => 'customer.claims.edit.engineering',
            // 'liability'        => 'customer.claims.edit.liability',
        ];

        $view = $viewMap[$claim->claim_type] ?? null;

        if (! $view) {
            return redirect()->route('claims.show', $claim)
                ->with('error', 'No edit form available for this claim type.');
        }

        // Add this — policy is already loaded, just make it available to the view
        $policy = $claim->policy;

        // Build hte form Data
        $formData = array_merge(
            $claim->form_data ?? [],
            [
                'fullname' => $customer->name ?? '',
                'email'    => $customer->email ?? '',
                'phone'    => $customer->phone ?? '',
                // 'occupation'    => $customer->occupation ?? '',
            ]
        );

        return view($view, compact('claim', 'policy', 'formData'));
    }

    public function update(Request $request, Claim $claim)
    {
        $validated = $request->validate([
            'claim_type'         => 'required|string',
            'form_data'          => 'required|array',
            'documents'          => 'nullable|array',
            'documents.*'        => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
            'delete_documents'   => 'nullable|array',
            'delete_documents.*' => 'integer|exists:claim_documents,id',
        ]);

        $editableStatuses = ['submitted', 'pending_info'];
        if (! in_array($claim->status, $editableStatuses)) {
            return response()->json([
                'success' => false,
                'message' => 'This claim can no longer be edited.',
            ], 403);
        }

        // Update form data
        $claim->update([
            'form_data' => $validated['form_data'],
        ]);

        // Delete marked documents
        if (! empty($validated['delete_documents'])) {
            $docsToDelete = \App\Models\ClaimDocument::whereIn('id', $validated['delete_documents'])
                ->where('claim_id', $claim->id) // safety — only delete docs belonging to this claim
                ->get();

            foreach ($docsToDelete as $doc) {
                Storage::disk('local')->delete($doc->file_path);
                $doc->delete();
            }
        }

        // Attach new documents
        if ($request->hasFile('documents')) {
            $this->claimService->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedBy: null,
                type: 'supporting',
            );
        }

        $this->claimService->logActivityPublic($claim, null, 'form_updated', 'Customer updated claim form data.');

        return response()->json([
            'success'      => true,
            'message'      => 'Your claim has been updated successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('claims.show', $claim),
        ]);
    }

    public function previewDocument(ClaimDocument $document, Request $request)
    {
        // Verify the document belongs to a claim owned by this customer
        // (skip this check on staff routes — add middleware instead)
        $path = Storage::disk('local')->path($document->file_path);

        if (! file_exists($path)) {
            Log::error('Document not found at path: ' . $path);
            abort(404, 'Document not found.');
        }

        if ($request->boolean('download')) {
            return response()->download($path, $document->original_name);
        }

        return response()->file($path, [
            'Content-Type'        => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"',
        ]);
    }

    public function cancel(Request $request, Claim $claim)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        // Ensure the claim belongs to this customer
        if ($claim->customer_id !== Auth::guard('customer')->user()->id) {
            abort(403);
        }

        if (! in_array($claim->status, ClaimStatus::cancellable())) {
            return back()->with('error', 'This claim cannot be cancelled in its current status.');
        }

        $this->claimService->cancel(
            claim: $claim,
            cancelledBy: Auth::guard('customer')->user(),
            note: $request->note,
        );

        return back()->with('success', 'Your claim has been reset to Submitted.');
    }
}
