<?php

namespace App\Http\Controllers\Api\V1\Staff;

use App\Enums\ClaimStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StaffClaimResource;
use App\Models\Claim;
use App\Models\ClaimDocument;
use App\Models\ClaimInfoRequest;
use App\Models\User;
use App\Services\ClaimService;
use App\Support\ApiResponse;
use App\Support\ClaimFormSchema;
use App\Support\StaffAccess;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Dedoc\Scramble\Attributes\Group;

#[Group('Staff - Claims', weight: 20)]
class ClaimController extends Controller
{
    private const DETAIL = [
        'customer',
        'policy',
        'branch',
        'assignedTo',
        'surveyor',
        'committeeDecidedBy',
        'documents',
        'activities.user',
        'activities.customer',
        'activities.agent',
        'infoRequests.requester',
    ];

    public function __construct(private ClaimService $claims) {}

    // ── Reading ───────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $request->validate([
            'scope'         => ['nullable', Rule::in(['incoming', 'mine'])],
            'search'        => ['nullable', 'string', 'max:100'],
            'status'        => ['nullable', Rule::in(ClaimStatus::all())],
            'branch'        => ['nullable', 'integer'],
            'updated_since' => ['nullable', 'date'],
            'per_page'      => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Claim::with(['customer', 'policy', 'assignedTo', 'branch']);

        if ($request->input('scope', 'incoming') === 'mine') {
            $query->where('assigned_to', $request->user()->id)
                ->whereNotIn('status', ClaimStatus::notActive());
        } else {
            // Same rule as the web incoming page: new claims, plus finished surveys nobody can pick up
            $query->where(function ($q) {
                $q->where('status', ClaimStatus::SUBMITTED)
                    ->orWhere(function ($q) {
                        $q->where('status', ClaimStatus::SURVEY_COMPLETED)
                            ->where(function ($q) {
                                $q->whereNull('assigned_to')
                                    ->orWhereHas('assignedTo', fn($u) => $u->where('is_active', false));
                            });
                    });
            });
        }

        $claims = $query
            ->search($request->input('search'))
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('branch'), fn($q) => $q->where('branch_id', $request->branch))
            ->when($request->filled('updated_since'), fn($q) => $q->where('updated_at', '>=', Carbon::parse($request->updated_since)))
            ->orderByDesc('updated_at')->orderByDesc('id')
            ->paginate($request->integer('per_page', 15))
            ->withQueryString();

        return StaffClaimResource::collection($claims);
    }

    public function show(int $claim)
    {
        return new StaffClaimResource(Claim::with(self::DETAIL)->findOrFail($claim));
    }

    public function document(Request $request, int $document)
    {
        $document = ClaimDocument::findOrFail($document);
        $disk     = Storage::disk('local');

        if (! $disk->exists($document->file_path)) {
            return ApiResponse::error('not_found', 'Document file not found.', 404);
        }

        return $disk->response(
            $document->file_path,
            $document->original_name,
            ['X-Content-Type-Options' => 'nosniff', 'Content-Type' => $document->mime_type],
            $request->boolean('download') ? 'attachment' : 'inline'
        );
    }

    // ── Assignment ────────────────────────────────────────────────────────

    public function process(Request $request, int $claim)
    {
        $user = $request->user();

        if (! StaffAccess::canHandle($user)) {
            $this->fail('forbidden_role', 'Your role cannot process claims.', 403);
        }

        $model = null;

        DB::transaction(function () use ($claim, $user, &$model) {
            $model = Claim::whereKey($claim)->lockForUpdate()->firstOrFail();   // two people can't both win

            if ($model->assigned_to) {
                $this->fail('already_assigned', 'This claim is already assigned.', 409);
            }

            if (! in_array($model->status, ClaimStatus::assignable(), true)) {
                $this->fail('not_assignable', 'This claim cannot be assigned at its current status.', 409);
            }

            $this->claims->assign($model, $user, $user, 'Self-assigned via API.');
        });

        return $this->detail($model);
    }

    public function assign(Request $request, int $claim)
    {
        $user = $request->user();
        $data = $request->validate([
            'assigned_to' => ['required', 'integer'],
            'note'        => ['nullable', 'string', 'max:500'],
        ]);

        if (! StaffAccess::canHandle($user)) {
            $this->fail('forbidden_role', 'Your role cannot assign claims.', 403);
        }

        $assignee = User::where('is_active', true)->find($data['assigned_to']);

        if (! $assignee || ! StaffAccess::canHandle($assignee)) {
            $this->fail('invalid_assignee', 'The assignee must be an active staff member who can handle claims.', 422);
        }

        $privileged = $user->isAdmin() || $user->isClaimHead();

        if (! $privileged && $assignee->id !== $user->id) {
            $this->fail('cannot_assign_others', 'Only an admin or claim head can assign a claim to someone else.', 403);
        }

        $model = null;

        DB::transaction(function () use ($claim, $user, $assignee, $privileged, $data, &$model) {
            $model = Claim::whereKey($claim)->lockForUpdate()->firstOrFail();

            if (! $privileged && $model->assigned_to && $model->assigned_to !== $user->id) {
                $this->fail('already_assigned', 'This claim belongs to someone else. Ask an admin or claim head to reassign it.', 403);
            }

            if (! in_array($model->status, ClaimStatus::assignable(), true)) {
                $this->fail('not_assignable', 'This claim cannot be reassigned at its current status.', 409);
            }

            $this->claims->assign($model, $assignee, $user, $data['note'] ?? null);
        });

        return $this->detail($model);
    }

    // ── Working on a claim ────────────────────────────────────────────────

    public function updateFormData(Request $request, int $claim)
    {
        $claim = $this->actable($request, $claim);
        $type  = $claim->claim_type;

        if (! ClaimFormSchema::has($type)) {
            $this->fail('no_form', 'This claim type cannot be edited through the API.', 409);
        }

        $data = $request->validate([
            /**
             * Only the fields to change. Fields you leave out keep their value.
             *
             * @var array<string, mixed>
             */
            'form_data' => ['required', 'array', 'min:1'],
            'note'      => ['nullable', 'string', 'max:500'],
        ]);

        if ($unknown = ClaimFormSchema::unknownFields($type, $data['form_data'])) {
            $this->fail('unknown_fields', 'form_data contains fields this form does not define: ' . implode(', ', $unknown) . '.', 422);
        }

        Validator::make(['form_data' => $data['form_data']], ClaimFormSchema::rules($type, partial: true))->validate();

        $changes = ClaimFormSchema::normalise($type, ClaimFormSchema::clean($type, $data['form_data']));

        if (($event = ClaimFormSchema::eventDate($type, $changes)) && $claim->policy) {
            if ($error = ClaimFormSchema::periodError($claim->policy, $event, applyWindow: false)) {
                $this->fail('event_outside_cover', $error, 422);
            }
        }

        $claim->update(['form_data' => array_merge($claim->form_data ?? [], $changes)]);

        $this->claims->logActivityPublic(
            claim: $claim,
            user: $request->user(),
            action: 'form_updated',
            note: $data['note'] ?? 'Form data updated by staff (API).',
            meta: ['updated_by' => $request->user()->id, 'fields' => array_keys($changes)],
        );

        return $this->detail($claim);
    }

    public function uploadDocuments(Request $request, int $claim)
    {
        $claim = $this->actable($request, $claim);
        $docs  = config('claim_forms.documents');

        $request->validate([
            'documents'   => ['required', 'array', 'max:' . $docs['max_files']],
            'documents.*' => ['file', 'max:' . $docs['max_size_kb'], 'mimes:' . implode(',', $docs['accepted'])],
        ]);

        $this->claims->attachDocuments(
            claim: $claim,
            files: $request->file('documents'),
            uploadedBy: $request->user(),
            type: 'staff_upload',
        );

        $claim->touch();

        return $this->detail($claim, 201);
    }

    public function requestInfo(Request $request, int $claim)
    {
        $claim = $this->actable($request, $claim);

        // Flatten whitespace first, like the web form, so the count matches the SMS
        $request->merge(['note' => preg_replace('/\s+/u', ' ', trim((string) $request->input('note')))]);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:' . ClaimInfoRequest::NOTE_MAX],
        ]);

        try {
            $this->claims->requestInfo($claim, $request->user(), $data['note']);
        } catch (\DomainException $e) {
            $this->fail('request_already_open', $e->getMessage(), 409);
        }

        return $this->detail($claim, 201);
    }

    public function sendToSurvey(Request $request, int $claim)
    {
        $claim = $this->actable($request, $claim);
        $data  = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $this->requireRoutable($claim);
        $this->claims->sendToSurvey($claim, $request->user(), $data['note'] ?? null);

        return $this->detail($claim);
    }

    public function sendToCommittee(Request $request, int $claim)
    {
        $claim = $this->actable($request, $claim);
        $data  = $request->validate(['note' => ['nullable', 'string', 'max:500']]);

        $this->requireRoutable($claim);
        $this->claims->sendToCommittee($claim, $request->user(), $data['note'] ?? null);

        return $this->detail($claim);
    }

    public function finalize(Request $request, int $claim)
    {
        $claim = $this->actable($request, $claim);
        $data  = $request->validate(['note' => ['nullable', 'string', 'max:1000']]);

        if (! $claim->isFinalizableBy($request->user())) {
            $this->fail('not_handler', 'You cannot finalize this claim.', 403);
        }

        if (! in_array($claim->status, ClaimStatus::decided(), true)) {
            $this->fail('not_decided', 'Only approved or rejected claims can be finalized.', 409);
        }

        $this->claims->finalize($claim, $request->user(), $data['note'] ?? null);

        return $this->detail($claim);
    }

    // ── Cancel and reopen ─────────────────────────────────────────────────

    public function cancel(Request $request, int $claim)
    {
        $claim = Claim::findOrFail($claim);
        $user  = $request->user();
        $data  = $request->validate(['note' => ['required', 'string', 'max:500']]);

        if (! in_array($claim->status, StaffAccess::cancellable($user, $claim), true)) {
            $this->fail('not_cancellable', 'You cannot cancel this claim at its current status.', 409);
        }

        $this->claims->cancel($claim, $user, $data['note']);

        return $this->detail($claim);
    }

    public function reopen(Request $request, int $claim)
    {
        $claim = Claim::findOrFail($claim);

        if (! $request->user()->isAdmin()) {
            $this->fail('forbidden_role', 'Only an admin can reopen a claim.', 403);
        }

        if ($claim->status !== ClaimStatus::CANCELLED) {
            $this->fail('not_cancelled', 'Only cancelled claims can be reopened.', 409);
        }

        $data = $request->validate(['note' => ['required', 'string', 'max:500']]);

        $this->claims->reopen($claim, $request->user(), $data['note']);

        return $this->detail($claim);
    }

    // ── internals ─────────────────────────────────────────────────────────

    /** The claim, if this staff member may work on it and it is assigned and editable. */
    private function actable(Request $request, int $id): Claim
    {
        $claim = Claim::with('policy')->findOrFail($id);

        if (! StaffAccess::canActOn($request->user(), $claim)) {
            $this->fail('not_handler', 'Only the assigned handler, a claim head or an admin can do this.', 403);
        }

        if (! $claim->canBeActedOn()) {
            $this->fail('claim_not_actionable', 'This claim must be assigned and in an editable status.', 409);
        }

        return $claim;
    }

    private function requireRoutable(Claim $claim): void
    {
        if (! in_array($claim->status, ClaimStatus::routable(), true)) {
            $this->fail('invalid_status', 'This claim cannot be routed from its current status.', 409);
        }
    }

    private function detail(Claim $claim, int $status = 200)
    {
        return (new StaffClaimResource($claim->refresh()->load(self::DETAIL)))
            ->response()->setStatusCode($status);
    }

    private function fail(string $code, string $message, int $status): never
    {
        throw new HttpResponseException(ApiResponse::error($code, $message, $status));
    }
}
