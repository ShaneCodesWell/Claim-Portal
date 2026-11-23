<?php

namespace App\Http\Resources\Api\V1;

use App\Enums\ClaimStatus;
use App\Support\ClaimFormSchema;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

/** @mixin \App\Models\Claim */
class ClaimResource extends JsonResource
{
    // Only these events are shown to customers, and only these keep their note.
    private const TIMELINE = [
        'submitted'          => 'Claim submitted',
        'staff_initiated'    => 'Claim opened on your behalf',
        'assigned'           => 'Claim assigned for review',
        'info_requested'     => 'More information requested',
        'info_provided'      => 'Information provided',
        'documents_uploaded' => 'Documents uploaded',
        'form_updated'       => 'Claim details updated',
        'sent_to_survey'     => 'Sent for survey',
        'survey_completed'   => 'Survey completed',
        'sent_to_committee'  => 'Sent to the claims committee',
        'committee_decision' => 'Decision made',
        'finalized'          => 'Claim closed',
        'cancelled'          => 'Claim cancelled',
        'reopened'           => 'Claim reopened',
    ];

    private const NOTE_VISIBLE = ['info_requested', 'info_provided'];

    public function toArray(Request $request): array
    {
        $form = $this->form_data ?? [];

        return [
            'id'                   => $this->id,
            'claim_number'         => $this->claim_number,
            'status'               => $this->status,
            'status_label'         => ClaimStatus::labels()[$this->status] ?? $this->status,
            'action_required'      => $this->status === ClaimStatus::PENDING_INFO,
            'can_cancel' => in_array($this->status, ClaimStatus::customerCancellable(), true),
            'claim_type'           => $this->claim_type,
            /** Sum insured at submission. This is not an approved amount. */
            'amount'               => $this->amount,
            /** Registration number of the vehicle the claim is for, when filed on a fleet policy. */
            'risk_id'              => isset($form['_risk_id']) ? ($form['registration_no'] ?? null) : null,
            'submitted_at'         => $this->submitted_at?->toIso8601String(),
            'updated_at'           => $this->updated_at?->toIso8601String(),
            'policy'               => $this->whenLoaded('policy', fn() => $this->policy ? [
                'id'             => $this->policy->id,
                'policy_number'  => $this->policy->policy_number,
                'product_name'   => $this->policy->product_name,
                'business_class' => $this->policy->business_class_name,
            ] : null),
            'pending_info_request' => $this->whenLoaded('pendingInfoRequest', fn() => $this->pendingInfoRequest ? [
                'id'           => $this->pendingInfoRequest->id,
                'note'         => $this->pendingInfoRequest->note,
                'requested_at' => $this->pendingInfoRequest->created_at?->toIso8601String(),
            ] : null),
            'form_data' => $this->when($request->routeIs('api.v1.claims.show', 'api.v1.claims.store'), fn() => ClaimFormSchema::present($this->claim_type, Arr::except($form, ['_risk_id']))),
            'documents'            => ClaimDocumentResource::collection($this->whenLoaded('documents')),
            /** @var array<int, array{event: string, label: string, note?: string, at: string|null}> */
            'timeline'             => $this->whenLoaded('activities', fn() => $this->activities
                ->filter(fn($a) => isset(self::TIMELINE[$a->action]))
                ->map(fn($a) => array_filter([
                    'event' => $a->action,
                    'label' => self::TIMELINE[$a->action],
                    'note'  => in_array($a->action, self::NOTE_VISIBLE, true) ? $a->note : null,
                    'at'    => $a->created_at?->toIso8601String(),
                ], fn($v) => $v !== null))
                ->values()),
        ];
    }
}
