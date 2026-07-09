<?php

namespace App\Http\Controllers\Customer;

use App\Enums\ClaimSource;
use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\ClaimDraft;
use App\Models\ClaimDraftDocument;
use App\Models\Policy;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function __construct(protected ClaimService $claimService) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_id'   => 'required',
            'claim_type'  => 'required|string',
            'form_data'   => 'required|array',
            'documents'   => 'nullable|array', // add these two
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return response()->json(['success' => false, 'message' => 'Session expired. Please log in again.'], 401);
        }

        $policy = Policy::whereIn('customer_id', $customer->resolvedCustomerIds())
            ->where(function ($q) use ($validated) {
                $q->where('external_policy_id', $validated['policy_id'])
                    ->orWhere('id', $validated['policy_id']);
            })
            ->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found. Please go back and select your policy again.'], 404);
        }

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

        // Attach documents if any were uploaded
        if ($request->hasFile('documents')) {
            $this->claimService->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedBy: null,
                type: 'supporting',
                uploadedByCustomer: $customer,
            );
        }

        // If there are lingering drafts Delete them.
        $customerIds = $customer->resolvedCustomerIds();

        $draft = ClaimDraft::whereIn('customer_id', $customerIds)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $validated['claim_type'])
            ->first();

        if ($draft) {
            foreach ($draft->documents as $doc) {
                $newPath = str_replace('claim-drafts/' . $draft->id, 'claims/' . $claim->id, $doc->file_path);
                Storage::disk('local')->move($doc->file_path, $newPath);

                $claim->documents()->create([
                    'uploaded_by'   => null,
                    'type'          => $doc->type,
                    'original_name' => $doc->original_name,
                    'file_path'     => $newPath,
                    'mime_type'     => $doc->mime_type,
                    'file_size'     => $doc->file_size,
                ]);
            }
            $draft->delete();
        }

        return response()->json([
            'success'      => true,
            'message'      => 'Your claim has been submitted successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('claims.index'),
        ]);
    }

    public function index()
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        $claims = Claim::whereIn('customer_id', $customerIds)
            ->whereNotIn('status', ClaimStatus::notActive())
            ->with(['policy'])
            ->latest()
            ->paginate(5);

        return view('customer.claims.index', compact('claims', 'customer'));
    }

    public function show(Claim $claim)
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($claim->customer_id, $customerIds)) {
            abort(403);
        }

        $claim->load(['policy', 'activities.user', 'documents']);
        return view('customer.claims.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        $claim->load(['policy', 'activities.user', 'documents', 'customer']);

        // Load the customer Data for display
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($claim->customer_id, $customerIds)) {
            abort(403);
        }

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
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($claim->customer_id, $customerIds)) {
            abort(403);
        }

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
                uploadedByCustomer: $customer,
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

    public function uploadDocuments(Request $request, Claim $claim)
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($claim->customer_id, $customerIds)) {
            abort(403);
        }

        $request->validate([
            'documents'   => 'required|array',
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $this->claimService->attachDocuments(
            claim: $claim,
            files: $request->file('documents'),
            uploadedBy: Auth::user(),
            type: 'survey_document',
            uploadedByCustomer: $customer,
        );

        return back()->with('success', count($request->file('documents')) . ' document(s) uploaded.');
    }

    public function previewDocument(ClaimDocument $document, Request $request)
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($document->claim->customer_id, $customerIds)) {
            abort(403);
        }

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

    public function destroyDocument(ClaimDocument $document): \Illuminate\Http\RedirectResponse
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        // Ensure the document belongs to a claim owned by this customer
        if (! in_array($document->claim->customer_id, $customerIds)) {
            abort(403);
        }

        // Only allow deletion if the claim is still editable
        if (! in_array($document->claim->status, ['submitted', 'pending_info'])) {
            return back()->with('error', 'Documents can no longer be removed from this claim.');
        }

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document removed successfully.');
    }

    public function cancel(Request $request, Claim $claim)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        // Ensure the claim belongs to this customer
        $customerIds = Auth::guard('customer')->user()->resolvedCustomerIds();
        if (! in_array($claim->customer_id, $customerIds)) {
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

        return back()->with('success', 'Your claim has been cancelled successfully.');
    }

    // Claim Drafts
    public function drafts()
    {
        $customer = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        $drafts = ClaimDraft::with('documents')
            ->whereIn('customer_id', $customerIds)
            ->latest()
            ->paginate(5);

        return view('customer.claims.draft.index', compact('drafts'));
    }

    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'policy_id'   => 'required',
            'claim_type'  => 'required|string',
            'form_data'   => 'nullable|array',
            'documents'   => 'nullable|array',
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        $policy = Policy::whereIn('customer_id', $customerIds)
            ->where(function ($q) use ($validated) {
                $q->where('external_policy_id', $validated['policy_id'])
                    ->orWhere('id', $validated['policy_id']);
            })
            ->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        $riskId = $request->input('risk_id') ? (int) $request->input('risk_id') : null;

        // Look up any existing draft across ALL of this person's resolved customer records
        $draft = ClaimDraft::whereIn('customer_id', $customerIds)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $validated['claim_type'])
            ->first();

        if ($draft) {
            $draft->update([
                'risk_id'       => $riskId,
                'form_data'     => $validated['form_data'] ?? [],
                'last_saved_at' => now(),
            ]);
        } else {
            // New draft — attach to whichever customer record is currently authenticated
            $draft = ClaimDraft::create([
                'customer_id'   => $customer->id,
                'policy_id'     => $policy->id,
                'claim_type'    => $validated['claim_type'],
                'risk_id'       => $riskId,
                'form_data'     => $validated['form_data'] ?? [],
                'last_saved_at' => now(),
            ]);
        }

        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('claim-drafts/' . $draft->id, 'local');

                $draft->documents()->create([
                    'original_name' => $file->getClientOriginalName(),
                    'file_path'     => $path,
                    'mime_type'     => $file->getMimeType(),
                    'file_size'     => $file->getSize(),
                    'type'          => 'supporting',
                ]);
            }
        }

        return response()->json([
            'success'  => true,
            'message'  => 'Progress saved. You can continue this claim later.',
            'draft_id' => $draft->id,
            'saved_at' => $draft->last_saved_at,
        ]);
    }

    public function getDraft(Request $request)
    {
        $validated = $request->validate([
            'policy_id'  => 'required',
            'claim_type' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();

        $policy = Policy::whereIn('customer_id', $customer->resolvedCustomerIds())
            ->where(function ($q) use ($validated) {
                $q->where('external_policy_id', $validated['policy_id'])
                    ->orWhere('id', $validated['policy_id']);
            })
            ->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        $customerIds = $customer->resolvedCustomerIds();

        $draft = ClaimDraft::with('documents')
            ->whereIn('customer_id', $customerIds)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $validated['claim_type'])
            ->first();

        if (! $draft) {
            return response()->json(['success' => true, 'draft' => null]);
        }

        return response()->json([
            'success' => true,
            'draft'   => [
                'id'         => $draft->id,
                'form_data'  => $draft->form_data,
                'risk_id'    => $draft->risk_id,
                'saved_at'   => $draft->last_saved_at,
                'documents'  => $draft->documents->map(fn($d) => [
                    'id'   => $d->id,
                    'name' => $d->original_name,
                    'url'  => route('customer.claims.draft.documents.preview', $d),
                ]),
            ],
        ]);
    }

    public function destroyDraft(Request $request)
    {
        $validated = $request->validate([
            'policy_id'  => 'required',
            'claim_type' => 'required|string',
        ]);

        $customer = Auth::guard('customer')->user();
        $policy = Policy::whereIn('customer_id', $customer->resolvedCustomerIds())
            ->where(function ($q) use ($validated) {
                $q->where('external_policy_id', $validated['policy_id'])
                    ->orWhere('id', $validated['policy_id']);
            })
            ->first();
        $customerIds = $customer->resolvedCustomerIds();

        $draft = ClaimDraft::whereIn('customer_id', $customerIds)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $validated['claim_type'])
            ->first();

        if ($draft) {
            foreach ($draft->documents as $doc) {
                Storage::disk('local')->delete($doc->file_path);
            }
            $draft->delete();
        }

        return response()->json(['success' => true]);
    }

    public function continueDraft(ClaimDraft $draft)
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($draft->customer_id, $customerIds)) {
            abort(403);
        }

        $policy = $draft->policy;

        // Extend this map as more claim-type forms come online.
        // Mirrors the viewMap comment in ClaimController::edit() — same limitation applies here.
        $routeMap = [
            'motor' => 'motor-form',
            'fire'             => 'fire-form',
            'general_accident' => 'general-accident-form',
        ];

        $routeName = $routeMap[$draft->claim_type] ?? null;

        if (! $routeName) {
            return redirect()->route('claims.draft.index')
                ->with('error', 'This claim type doesn\'t have an online form available yet.');
        }

        return redirect()->route($routeName, [
            'policyId' => $policy->external_policy_id ?? $policy->id,
            'riskId'   => $draft->risk_id,
        ]);
    }

    public function destroyDraftById(ClaimDraft $draft)
    {
        $customer    = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($draft->customer_id, $customerIds)) {
            abort(403);
        }

        foreach ($draft->documents as $doc) {
            Storage::disk('local')->delete($doc->file_path);
        }

        $draft->delete();

        return back()->with('success', 'Draft deleted successfully.');
    }

    public function previewDraftDocument(ClaimDraftDocument $document, Request $request)
    {
        $customer = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($document->draft->customer_id, $customerIds)) {
            abort(403);
        }

        $path = Storage::disk('local')->path($document->file_path);

        if (! file_exists($path)) {
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

    public function destroyDraftDocument(ClaimDraftDocument $document)
    {
        $customer = Auth::guard('customer')->user();
        $customerIds = $customer->resolvedCustomerIds();

        if (! in_array($document->draft->customer_id, $customerIds)) {
            abort(403);
        }

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true]);
    }
}
