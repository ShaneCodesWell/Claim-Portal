<?php

namespace App\Http\Controllers\Agent;

use App\Enums\ClaimSource;
use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\ClaimDraft;
use App\Models\ClaimDraftDocument;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\ClaimNotificationService;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ClaimController extends Controller
{
    public function __construct(
        protected ClaimService $claimService,
        private ClaimNotificationService $notificationService,
    ) {}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'policy_id'   => 'required',
            'claim_type'  => 'required|string',
            'form_data'   => 'required|array',
            'documents'   => 'nullable|array',
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
            'note'        => 'nullable|string|max:1000',
        ]);

        $agent = Auth::guard('agent')->user();

        $policy = Policy::where(function ($q) use ($validated) {
            $q->where('external_policy_id', $validated['policy_id'])
                ->orWhere('id', $validated['policy_id']);
        })->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        $customer = Customer::findOrFail($policy->customer_id);

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
            source: ClaimSource::AGENT_PORTAL,
            riskId: $riskId,
        );

        // Agent initiation tracking
        $claim->update([
            'initiated_by_agent' => true,
            'initiated_by_agent_id' => $agent->id,
        ]);

        // Activity log
        $note = trim($validated['note'] ?? '');
        $this->claimService->logActivityPublic(
            claim: $claim,
            user: $agent,
            action: 'agent_initiated',
            note: "Claim initiated by intermediary {$agent->name} on behalf of {$customer->name}."
                . ($note ? " Agent note: {$note}" : ''),
            meta: [
                'on_behalf_of_customer_id' => $customer->id,
                'initiated_by_agent_id'    => $agent->id,
            ]
        );

        if ($request->hasFile('documents')) {
            $this->claimService->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedByAgent: $agent,
                type: 'supporting',
            );
        }

        $this->notificationService->notifyAgentInitiated($claim, $agent);

        return response()->json([
            'success' => true,
            'message' => "Claim {$claim->claim_number} submitted on behalf of {$customer->name}.",
            'claim_number' => $claim->claim_number,
            'redirect' => route('agent.claims.show', $claim),
        ]);
    }

    public function index()
    {
        $customer = Customer::where('phone', session('phone_number') ?? session('mobile_no'))
            ->orWhere('external_customer_code', session('customer_code'))
            ->first();

        $claims = Claim::where('customer_id', $customer?->id)
            ->with(['policy'])
            ->latest()
            ->paginate(5);

        return view('agent.claims.index', compact('claims', 'customer'));
    }

    public function create(Request $request)
    {
        $policyId = $request->query('policy_id');
        $riskId   = $request->query('risk_id');

        $policy = Policy::where(function ($q) use ($policyId) {
            $q->where('external_policy_id', $policyId)
                ->orWhere('id', $policyId);
        })->firstOrFail();

        $customer = Customer::findOrFail($policy->customer_id);
        $agent    = Auth::guard('agent')->user();

        $claimType = $this->normalizeClaimType($policy->business_class_name ?? '');

        $viewMap = [
            'motor'            => ['partial' => 'partials.forms.motor-form', 'label' => 'Motor'],
            'fire'             => ['partial' => 'partials.forms.fire-form', 'label' => 'Fire'],
            'general_accident' => ['partial' => 'partials.forms.general-accident-form', 'label' => 'General Accident'],
        ];

        if (! isset($viewMap[$claimType])) {
            return redirect()
                ->route('agent.dashboard.index')
                ->with('error', "No claim form available for policy type: {$policy->business_class}.");
        }

        // Look for an in-progress draft this agent already started for this policy/claim type
        $draft = ClaimDraft::where('agent_id', $agent->id)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $claimType)
            ->first();

        $formData = array_merge(
            [
                'fullname' => $customer->name ?? '',
                'email'    => $customer->email ?? '',
                'phone'    => $customer->phone ?? '',
            ],
            $policy->vehicleFormData($riskId ? (int) $riskId : null)
        );

        // Draft data overrides the freshly-pulled policy/customer defaults
        if ($draft) {
            $formData = array_merge($formData, $draft->form_data ?? []);
            $riskId   = $draft->risk_id ?? $riskId;
        }

        return view('agent.claims.create', [
            'customer' => $customer,
            'policy'   => $policy,
            'riskId'   => $riskId,
            'formView' => $viewMap[$claimType]['partial'],
            'action'   => route('agent.claims.store'),
            'method'   => 'POST',
            'claim'    => null,
            'draft'    => $draft,
            'context'  => 'agent',
            'formData' => $formData,
        ]);
    }

    public function show(Claim $claim)
    {
        $claim->load(['policy', 'activities.user', 'documents']);
        return view('agent.claims.show', compact('claim'));
    }

    public function edit(Claim $claim)
    {
        $claim->load(['policy', 'documents', 'customer']);

        // Only claims this agent actually initiated can be edited by them
        if ($claim->initiated_by_agent_id !== Auth::guard('agent')->id()) {
            abort(403, 'You do not have access to this claim.');
        }

        if (! $claim->isEditable()) {
            abort(403, 'This claim can no longer be edited.');
        }

        $policy = $claim->policy;
        $customer = $claim->customer;

        $viewMap = [
            'motor'            => ['partial' => 'partials.forms.motor-form', 'label' => 'Motor'],
            'fire'             => ['partial' => 'partials.forms.fire-form', 'label' => 'Fire'],
            'general_accident' => ['partial' => 'partials.forms.general-accident-form', 'label' => 'General Accident'],
        ];

        if (! isset($viewMap[$claim->claim_type])) {
            return redirect()->route('agent.claims.show', $claim)
                ->with('error', 'No edit form available for this claim type.');
        }

        $formData = array_merge(
            [
                'fullname' => $customer->name ?? '',
                'email'    => $customer->email ?? '',
                'phone'    => $customer->phone ?? '',
            ],
            $claim->form_data ?? []
        );

        return view('agent.claims.create', [
            'customer' => $customer,
            'policy'   => $policy,
            'riskId'   => $claim->form_data['_risk_id'] ?? null,
            'formView' => $viewMap[$claim->claim_type]['partial'],
            'action'   => route('agent.claims.update', $claim),
            'method'   => 'PUT',
            'claim'    => $claim,
            'draft'    => null,
            'context'  => 'agent',
            'formData' => $formData,
        ]);
    }

    public function update(Request $request, Claim $claim)
    {
        $agent = Auth::guard('agent')->user();

        if ($claim->initiated_by_agent_id !== $agent->id) {
            abort(403, 'You do not have access to this claim.');
        }

        $validated = $request->validate([
            'claim_type'          => 'required|string',
            'form_data'           => 'required|array',
            'documents'           => 'nullable|array',
            'documents.*'         => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
            'delete_documents'    => 'nullable|array',
            'delete_documents.*'  => 'integer|exists:claim_documents,id',
            'note'                => 'nullable|string|max:500',
        ]);

        if (! $claim->isEditable()) {
            return response()->json([
                'success' => false,
                'message' => 'This claim can no longer be edited.',
            ], 403);
        }

        $claim->update(['form_data' => $validated['form_data']]);

        if (! empty($validated['delete_documents'])) {
            $docsToDelete = ClaimDocument::whereIn('id', $validated['delete_documents'])
                ->where('claim_id', $claim->id)
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
                uploadedByAgent: $agent,
                type: 'agent_upload',
            );
        }

        $this->claimService->logActivityPublic(
            $claim,
            $agent,
            'form_updated',
            $validated['note'] ?? "Form data updated by intermediary {$agent->name}.",
            [
                'updated_by_agent_id' => $agent->id,
            ]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Claim updated successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('agent.claims.show', $claim),
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

    private function normalizeClaimType(string $businessClass): string
    {
        return str_replace(' ', '_', strtolower(trim($businessClass)));
    }

    // Claim Drafts for Agent
    public function drafts()
    {
        $agent = Auth::guard('agent')->user();

        $drafts = ClaimDraft::with('documents')
            ->where('agent_id', $agent->id)
            ->latest()
            ->paginate(5);

        return view('agent.claims.draft.index', compact('drafts'));
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

        $agent = Auth::guard('agent')->user();

        $policy = Policy::where(function ($q) use ($validated) {
            $q->where('external_policy_id', $validated['policy_id'])
                ->orWhere('id', $validated['policy_id']);
        })->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        $riskId = $request->input('risk_id') ? (int) $request->input('risk_id') : null;

        $draft = ClaimDraft::where('agent_id', $agent->id)
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
            $draft = ClaimDraft::create([
                'customer_id'   => $policy->customer_id,
                'policy_id'     => $policy->id,
                'agent_id'      => $agent->id,
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

        $agent = Auth::guard('agent')->user();

        $policy = Policy::where(function ($q) use ($validated) {
            $q->where('external_policy_id', $validated['policy_id'])
                ->orWhere('id', $validated['policy_id']);
        })->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        $draft = ClaimDraft::with('documents')
            ->where('agent_id', $agent->id)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $validated['claim_type'])
            ->first();

        if (! $draft) {
            return response()->json(['success' => true, 'draft' => null]);
        }

        return response()->json([
            'success' => true,
            'draft'   => [
                'id'        => $draft->id,
                'form_data' => $draft->form_data,
                'risk_id'   => $draft->risk_id,
                'saved_at'  => $draft->last_saved_at,
                'documents' => $draft->documents->map(fn($d) => [
                    'id'   => $d->id,
                    'name' => $d->original_name,
                    'url'  => route('agent.claims.draft.documents.preview', $d),
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

        $agent = Auth::guard('agent')->user();

        $policy = Policy::where(function ($q) use ($validated) {
            $q->where('external_policy_id', $validated['policy_id'])
                ->orWhere('id', $validated['policy_id']);
        })->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
        }

        $draft = ClaimDraft::where('agent_id', $agent->id)
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
        $agent = Auth::guard('agent')->user();

        if ($draft->agent_id !== $agent->id) {
            abort(403);
        }

        $policy = $draft->policy;

        // Agent's create() auto-detects claim type from the policy itself,
        // so we don't need the routeMap the customer flow uses.
        return redirect()->route('agent.claims.create', [
            'policy_id' => $policy->external_policy_id ?? $policy->id,
            'risk_id'   => $draft->risk_id,
        ]);
    }

    public function destroyDraftById(ClaimDraft $draft)
    {
        $agent = Auth::guard('agent')->user();

        if ($draft->agent_id !== $agent->id) {
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
        $agent = Auth::guard('agent')->user();

        if ($document->draft->agent_id !== $agent->id) {
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
        $agent = Auth::guard('agent')->user();

        if ($document->draft->agent_id !== $agent->id) {
            abort(403);
        }

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return response()->json(['success' => true]);
    }
}
