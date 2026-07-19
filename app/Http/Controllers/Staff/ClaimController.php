<?php

namespace App\Http\Controllers\Staff;

use App\Enums\ClaimSource;
use App\Enums\ClaimStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\User;
use App\Services\ClaimNotificationService;
use App\Services\ClaimService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use \Illuminate\Http\RedirectResponse;

class ClaimController extends Controller
{
    public function __construct(
        private ClaimService $claimService,
        private ClaimNotificationService $notificationService,
    ) {}

    public function index(Request $request)
    {
        $branches = \App\Models\Branch::where('is_active', true)
            ->orderBy('name')
            ->get();

        $query = Claim::with(['customer', 'policy', 'assignedTo', 'branch'])
            ->whereIn('status', [
                ClaimStatus::SUBMITTED,
                ClaimStatus::SURVEY_COMPLETED,
            ])
            ->latest();

        match ($request->filter) {
            'low'    => $query->where('amount', '<=', 30000),
            'medium' => $query->whereBetween('amount', [30001, 100000]),
            'high'   => $query->where('amount', '>', 100000),
            default  => null,
        };

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('policy', fn($q) => $q->where('policy_number', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('branch')) {
            $query->where('branch_id', $request->branch);
        }

        $claims = $query->paginate(5)->withQueryString();

        return view('staff.claims.index', compact('claims', 'branches'));
    }

    public function myQueue()
    {
        $claims = Claim::where('assigned_to', Auth::user()->id)
            ->whereNotIn('status', ClaimStatus::terminal())
            ->with(['customer', 'policy'])
            ->latest()
            ->paginate(15);

        $stats = [
            'total_claims'  => Claim::where('assigned_to', Auth::user()->id)->where('status', '!=', 'closed')->count(),
            'under_review'  => Claim::where('assigned_to', Auth::user()->id)->where('status', 'under_review')->count(),
            'closed_claims' => Claim::where('assigned_to', Auth::user()->id)->where('status', 'closed')->count(),
        ];

        return view('staff.claims.my-queue', compact('claims', 'stats'));
    }

    public function show(Claim $claim)
    {
        $claim->load(['customer', 'policy', 'assignedTo', 'branch', 'activities.staff', 'documents', 'surveyor', 'committeeDecidedBy']);

        $staffMembers = User::where('is_active', true)
            ->whereIn('role', UserRole::staffRoles())
            ->get();

        return view('staff.claims.show', compact('claim', 'staffMembers'));
    }

    public function create(Request $request, Customer $customer)
    {
        $policyId = $request->query('policy_id');
        $riskId   = $request->query('risk_id');

        $policy = Policy::where(function ($q) use ($policyId) {
            $q->where('external_policy_id', $policyId)
                ->orWhere('id', $policyId);
        })
            ->where('customer_id', $customer->id)
            ->firstOrFail();

        // Normalize to the same keys your form views expect
        $claimType = $this->normalizeClaimType($policy->business_class_name ?? '');

        $viewMap = [
            'motor'            => ['partial' => 'partials.forms.motor-form', 'label' => 'Motor'],
            'fire'             => ['partial' => 'partials.forms.fire-form', 'label' => 'Fire'],
            'general_accident' => ['partial' => 'partials.forms.general-accident-form', 'label' => 'General Accident'],
        ];

        if (! isset($viewMap[$claimType])) {
            return redirect()
                ->route('customers.show', $customer)
                ->with('error', "No claim form available for policy type: {$policy->business_class}.");
        }

        return view('staff.claims.create', [
            'customer' => $customer,
            'policy'   => $policy,
            'riskId'   => $riskId,
            'formView' => $viewMap[$claimType]['partial'],
            'action'   => route('customers.claims.store', $customer),
            'method'   => 'POST',
            'claim'    => null,
            'context'  => 'staff',
            'formData' => array_merge(
                [
                    'fullname' => $customer->name ?? '',
                    'email'    => $customer->email ?? '',
                    'phone'    => $customer->phone ?? '',
                ],
                $policy->vehicleFormData($riskId ? (int) $riskId : null)
            ),
        ]);
    }

    public function store(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'policy_id'   => 'required',
            'claim_type'  => 'required|string',
            'form_data'   => 'required|array',
            'documents'   => 'nullable|array',
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
            'note'        => 'nullable|string|max:1000',
        ]);

        $staff = Auth::user();

        $policy = Policy::where('customer_id', $customer->id)
            ->where(function ($q) use ($validated) {
                $q->where('external_policy_id', $validated['policy_id'])
                    ->orWhere('id', $validated['policy_id']);
            })->first();

        if (! $policy) {
            return response()->json(['success' => false, 'message' => 'Policy not found.'], 404);
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
            source: ClaimSource::STAFF_PORTAL,
            riskId: $riskId,
        );

        // Staff initiation tracking
        $claim->update([
            'initiated_by_staff' => true,
            'initiated_by'       => $staff->id,
        ]);

        // Activity log
        $note = trim($validated['note'] ?? '');
        $this->claimService->logActivityPublic(
            claim: $claim,
            user: $staff,
            action: 'staff_initiated',
            note: "Claim initiated by {$staff->name} on behalf of {$customer->name}."
                . ($note ? " Staff note: {$note}" : ''),
            meta: [
                'on_behalf_of_customer_id' => $customer->id,
                'initiated_by_staff_id'    => $staff->id,
                'via_policy_search'        => $request->query('via') === 'policy_search',
            ]
        );

        // Documents
        if ($request->hasFile('documents')) {
            $this->claimService->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedBy: $staff,
                type: 'supporting',
            );
        }

        // SMS — notify the customer
        $this->notificationService->notifyStaffInitiated($claim, $staff);

        return response()->json([
            'success' => true,
            'message' => "Claim {$claim->claim_number} submitted on behalf of {$customer->name}.",
            'claim_number' => $claim->claim_number,
            'redirect' => route('customers.show', $customer),
        ]);
    }

    private function normalizeClaimType(string $businessClass): string
    {
        return str_replace(' ', '_', strtolower(trim($businessClass)));
    }

    public function uploadDocuments(Request $request, Claim $claim)
    {
        $request->validate([
            'documents'   => 'required|array',
            'documents.*' => 'file|max:5120|mimes:jpg,jpeg,png,gif,pdf',
        ]);

        $this->claimService->attachDocuments(
            claim: $claim,
            files: $request->file('documents'),
            uploadedBy: Auth::user(),
            type: 'survey_document',
        );

        return back()->with('success', count($request->file('documents')) . ' document(s) uploaded.');
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

    public function destroyDocument(ClaimDocument $document): RedirectResponse
    {
        $staff = Auth::user();

        // Admins can delete any document; everyone else can only remove their own uploads
        if (! $staff->isAdmin() && $document->uploaded_by !== $staff->id) {
            return back()->with('error', 'You can only remove documents you uploaded.');
        }

        if (! in_array($document->claim->status, ['submitted', 'pending_info'])) {
            return back()->with('error', 'Documents can no longer be removed from this claim.');
        }

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        return back()->with('success', 'Document removed successfully.');
    }

    public function print(Claim $claim)
    {
        $claim->load(['customer', 'policy']);

        if (request()->header('X-Requested-With') === 'XMLHttpRequest') {
            try {
                return view('staff.claims.print', compact('claim'))->render();
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

        return view('staff.claims.print', compact('claim'));
    }

    public function process(Claim $claim)
    {
        if (! $claim->isEditable()) {
            abort(403, 'This claim can no longer be processed.');
        }

        if ($claim->assigned_to) {
            return back()->with('error', 'This claim is already assigned.');
        }

        $this->claimService->assign(
            claim: $claim,
            assignee: Auth::user(),
            assignedBy: Auth::user(),
            note: 'Self-assigned via Process Claim.',
        );

        return redirect()
            ->route('staff.claims.show', $claim)
            ->with('success', 'Claim assigned to you and moved to review.');
    }

    public function assign(Request $request, Claim $claim)
    {
        $request->validate([
            'assigned_to' => 'required|exists:users,id',
            'note'        => 'nullable|string|max:500',
        ]);

        $assignee = User::findOrFail($request->assigned_to);

        $this->claimService->assign(
            claim: $claim,
            assignee: $assignee,
            assignedBy: Auth::user(),
            note: $request->note,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Claim assigned to {$assignee->name}.",
            ]);
        }

        return back()->with('success', "Claim assigned to {$assignee->name}.");
    }

    public function updateStatus(Request $request, Claim $claim)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', ClaimStatus::all()),
            'note'   => 'nullable|string|max:1000',
        ]);

        $this->claimService->updateStatus(
            claim: $claim,
            newStatus: $request->status,
            updatedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Claim status updated.');
    }

    public function requestInfo(Request $request, Claim $claim)
    {
        $request->validate([
            'note' => 'required|string|max:1000',
        ]);

        $this->claimService->requestInfo(
            claim: $claim,
            requestedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Information request sent.');
    }

    public function updateFormData(Request $request, Claim $claim)
    {
        $request->validate([
            'form_data' => 'required|array',
            'note'      => 'nullable|string|max:500',
        ]);

        $this->claimService->updateFormData(
            claim: $claim,
            newData: $request->form_data,
            updatedBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Claim details updated.');
    }

    public function edit(Claim $claim)
    {
        $claim->load(['policy', 'documents', 'assignedTo', 'customer']);
        $policy = $claim->policy;

        if (! $claim->isEditable()) {
            abort(403, 'This claim can no longer be edited.');
        }

        $viewMap = [
            'motor'            => 'staff.claims.edit.motor',
            'fire'             => 'staff.claims.edit.fire',
            'general_accident' => 'staff.claims.edit.general-accident',
        ];

        $view = $viewMap[$claim->claim_type] ?? null;

        if (! $view) {
            return redirect()->route('staff.claims.show', $claim)
                ->with('error', 'No edit form available for this claim type.');
        }

        $currentUser       = Auth::user();
        $assignee          = $claim->assignedTo; // null if unassigned
        $isAssignedToMe    = $assignee && $assignee->id === $currentUser->id;
        $isAssignedToOther = $assignee && ! $isAssignedToMe;

        $formData = array_merge(
            [
                'fullname' => $claim->customer->name ?? '',
                'email'    => $claim->customer->email ?? '',
                'phone'    => $claim->customer->phone ?? '',
            ],
            $claim->form_data ?? []
        );

        return view($view, compact(
            'claim',
            'policy',
            'assignee',
            'formData',
            'isAssignedToMe',
            'isAssignedToOther'
        ));
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
            'note'               => 'nullable|string|max:500',
        ]);

        $claim->update(['form_data' => $validated['form_data']]);

        if (! $claim->isEditable()) {
            abort(403, 'This claim can no longer be edited.');
        }

        // Delete marked documents
        if (! empty($validated['delete_documents'])) {
            $docsToDelete = ClaimDocument::whereIn('id', $validated['delete_documents'])
                ->where('claim_id', $claim->id)
                ->get();

            foreach ($docsToDelete as $doc) {
                Storage::disk('local')->delete($doc->file_path);
                $doc->delete();
            }
        }

        // Attach new documents — stamped with the staff user who uploaded them
        if ($request->hasFile('documents')) {
            $this->claimService->attachDocuments(
                claim: $claim,
                files: $request->file('documents'),
                uploadedBy: Auth::user(), // ← staff user recorded here
                type: 'staff_upload',
            );
        }

        $this->claimService->logActivityPublic(
            $claim,
            Auth::user(),
            'form_updated',
            $validated['note'] ?? 'Form data updated by staff.',
            [
                'updated_by'       => Auth::user()->id,
                'role'             => Auth::user()->role,
                'assigned_to'      => $claim->assigned_to,
                'assignment_state' => $claim->assigned_to
                    ? ($claim->assigned_to === Auth::id() ? 'assigned_to_self' : 'assigned_to_other')
                    : 'unassigned',
            ]
        );

        return response()->json([
            'success'      => true,
            'message'      => 'Claim updated successfully.',
            'claim_number' => $claim->claim_number,
            'redirect'     => route('staff.claims.show', $claim),
        ]);
    }

    public function finalize(Claim $claim, ClaimService $claimService): RedirectResponse
    {
        $user = Auth::user();

        if (! $claim->isFinalizableBy($user)) {
            abort(403, 'You do not have permission to finalize this claim.');
        }

        // Add this if the flow changes
        // if ($claim->status !== ClaimStatus::APPROVED) {
        //     return back()->with('error', 'Only approved claims can be finalized.');
        // }

        $claimService->finalize($claim, $user);

        return redirect()
            ->route('staff.claims.show', $claim)
            ->with('success', 'Claim has been finalized and processing is complete.');
    }

    public function cancel(Request $request, Claim $claim)
    {
        $request->validate([
            'note' => 'nullable|string|max:500',
        ]);

        // Guard: only cancellable statuses
        if (! in_array($claim->status, ClaimStatus::cancellable())) {
            return back()->with('error', 'This claim cannot be cancelled in at this point.');
        }

        $this->claimService->cancel(
            claim: $claim,
            cancelledBy: Auth::user(),
            note: $request->note,
        );

        return back()->with('success', 'Claim has been reset to Submitted.');
    }

    public function sendToSurvey(Request $request, Claim $claim)
    {
        $request->validate(['note' => 'nullable|string|max:500']);

        if (in_array($claim->status, ClaimStatus::terminal())) {
            return back()->with('error', 'This claim is already closed and cannot be sent to survey.');
        }

        $this->claimService->sendToSurvey($claim, Auth::user(), $request->note);

        return back()->with('success', 'Claim sent to survey.');
    }

    public function sendToCommittee(Request $request, Claim $claim)
    {
        $request->validate(['note' => 'nullable|string|max:500']);

        if (in_array($claim->status, ClaimStatus::terminal())) {
            return back()->with('error', 'This claim is already closed and cannot be escalated.');
        }

        $this->claimService->sendToCommittee($claim, Auth::user(), $request->note);

        return back()->with('success', 'Claim escalated to the Claims Committee.');
    }

    public function archive(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $query = Claim::with(['customer', 'policy', 'assignedTo', 'committeeDecidedBy'])
            ->whereIn('status', ClaimStatus::terminal());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('policy', fn($q) => $q->where('policy_number', 'like', "%{$search}%"));
            });
        }

        $claims = $query->latest()->paginate(15)->withQueryString();

        return view('staff.claims.archive', compact('claims'));
    }

    public function tracking(Request $request)
    {
        abort_unless(Auth::user()->isAdmin(), 403);

        $query = Claim::with(['customer', 'policy', 'assignedTo', 'surveyor', 'committeeDecidedBy'])
            ->whereIn('status', [
                ClaimStatus::UNDER_SURVEY,
                ClaimStatus::SURVEY_COMPLETED,
                ClaimStatus::COMMITTEE_REVIEW,
            ])
            ->latest();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($q) => $q->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('policy', fn($q) => $q->where('policy_number', 'like', "%{$search}%"));
            });
        }

        $claims = $query->paginate(15)->withQueryString();

        return view('staff.claims.tracking', compact('claims'));
    }
}
