<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\ClaimActivityAction;
use App\Enums\ClaimStatus;
use App\Support\ClaimFormSchema;
use App\Support\StaffAccess;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Claim */
class StaffClaimResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user   = $request->user();
        $detail = $this->relationLoaded('activities');   // the detail endpoints load the heavy relations

        return [
            'id'                    => $this->id,
            'claim_number'          => $this->claim_number,
            'status'                => $this->status,
            'status_label'          => ClaimStatus::labels()[$this->status] ?? $this->status,
            'claim_type'            => $this->claim_type,
            'source'                => $this->source,
            /** Sum insured at submission. This is not an approved amount. */
            'amount'                => $this->amount,
            /** Estimated repair cost, when the claim form provided one. */
            'estimated_repair_cost' => $this->estimated_repair_cost,
            'submitted_at'          => $this->submitted_at?->toIso8601String(),
            'updated_at'            => $this->updated_at?->toIso8601String(),
            'assigned_at'           => $this->assigned_at?->toIso8601String(),

            'customer' => $this->whenLoaded('customer', fn() => $this->customer ? [
                'id' => $this->customer->id,
                'name' => $this->customer->name,
                'phone' => $this->customer->phone,
                'email' => $this->customer->email,
            ] : null),
            'policy' => $this->whenLoaded('policy', fn() => $this->policy ? [
                'id' => $this->policy->id,
                'policy_number' => $this->policy->policy_number,
                'product_name' => $this->policy->product_name,
                'business_class' => $this->policy->business_class_name,
                'start_date' => $this->policy->start_date?->toDateString(),
                'end_date' => $this->policy->end_date?->toDateString(),
            ] : null),
            'branch'      => $this->whenLoaded('branch', fn() => $this->branch?->name),
            'assigned_to' => $this->whenLoaded('assignedTo', fn() => $this->assignedTo ? [
                'id' => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
                'email' => $this->assignedTo->email,
            ] : null),

            // What the calling staff member may do right now
            'permissions' => [
                'can_process' => $user && ! $this->assigned_to && StaffAccess::canHandle($user)
                    && in_array($this->status, ClaimStatus::assignable(), true),
                'can_act'     => $user && StaffAccess::canActOn($user, $this->resource) && $this->canBeActedOn(),
            ],

            'survey' => $this->when($detail, fn() => [
                'surveyor'     => $this->surveyor?->name,
                'notes'        => $this->survey_notes,
                'completed_at' => $this->survey_completed_at?->toIso8601String(),
            ]),
            'committee' => $this->when($detail, fn() => [
                'decision'   => in_array($this->status, ClaimStatus::decided(), true) ? $this->status : null,
                'notes'      => $this->committee_notes,
                'decided_by' => $this->committeeDecidedBy?->name,
                'decided_at' => $this->committee_decided_at?->toIso8601String(),
            ]),

            /** @var array<string, mixed> */
            'form_data' => $this->when($detail, fn() => ClaimFormSchema::present($this->claim_type, $this->form_data ?? [])),

            /** @var array<int, array{id: int, name: string, mime_type: string|null, size_bytes: int|null, type: string|null, uploaded_by: string|null, uploaded_at: string|null, url: string}> */
            'documents' => $this->when($detail, fn() => $this->documents->map(fn($d) => [
                'id'          => $d->id,
                'name'        => $d->original_name,
                'mime_type'   => $d->mime_type,
                'size_bytes'  => $d->file_size,
                'type'        => $d->type,
                'uploaded_by' => $d->uploaded_by ? 'staff' : ($d->uploaded_by_customer_id ? 'customer' : ($d->uploaded_by_agent_id ? 'agent' : null)),
                'uploaded_at' => $d->created_at?->toIso8601String(),
                'url'         => route('api.v1.staff.documents.show', $d->id),
            ])->values()),

            /** @var array<int, array{id: int, status: string, note: string, requested_by: string|null, requested_at: string|null, sms_status: string|null, response_note: string|null, responded_at: string|null}> */
            'info_requests' => $this->when($detail, fn() => $this->infoRequests->map(fn($r) => [
                'id'            => $r->id,
                'status'        => $r->status,
                'note'          => $r->note,
                'requested_by'  => $r->requester?->name,
                'requested_at'  => $r->created_at?->toIso8601String(),
                'sms_status'    => $r->sms_status,
                'response_note' => $r->response_note,
                'responded_at'  => $r->responded_at?->toIso8601String(),
            ])->values()),

            /** @var array<int, array{event: string, label: string, note: string|null, actor: string, actor_type: string, at: string|null}> */
            'timeline' => $this->when($detail, fn() => $this->activities->map(fn($a) => [
                'event'      => $a->action,
                'label'      => ClaimActivityAction::label($a->action),
                'note'       => $a->note,
                'actor'      => $a->actor_name,
                'actor_type' => $a->user_id ? 'staff' : ($a->customer_id ? 'customer' : ($a->agent_id ? 'agent' : 'system')),
                'at'         => $a->created_at?->toIso8601String(),
            ])->values()),
        ];
    }
}
