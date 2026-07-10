<?php

namespace App\Services;

use App\Enums\ClaimSource;
use App\Enums\ClaimStatus;
use App\Enums\UserRole;
use App\Models\Branch;
use App\Models\Claim;
use App\Models\ClaimActivity;
use App\Models\ClaimDocument;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ClaimService
{

    // Add to ClaimService constructor
    public function __construct(private ClaimNotificationService $notifications) {}

    // Register a new claim
    public function register(
        Customer $customer,
        Policy $policy,
        string $claimType,
        array $formData,
        string $source = ClaimSource::CUSTOMER_PORTAL,
        ?int $riskId = null,
    ): Claim {
        return DB::transaction(function () use ($customer, $policy, $claimType, $formData, $source, $riskId) {

            $claim = Claim::create([
                'claim_number' => Claim::generateClaimNumber(),
                'customer_id'  => $customer->id,
                'policy_id'    => $policy->id,
                'branch_id'    => $policy->branch_id ?? $this->resolveBranch($policy),
                'claim_type'   => $claimType,
                'source'       => $source,
                'status'       => ClaimStatus::SUBMITTED,
                'amount'       => $this->extractSumInsured($policy, $riskId),
                'form_data'    => $formData,
                'submitted_at' => now(),
            ]);

            // Log submission activity
            $this->logActivity($claim, null, 'submitted', 'Claim submitted via ' . ClaimSource::labels()[$source]);

            // Auto-assign based on branch
            $this->autoAssign($claim);

            // SMS — only for customer self-submissions.
            // Staff-initiated claims get notifyStaffInitiated() instead (called in staff ClaimController::store()).
            if ($source === ClaimSource::CUSTOMER_PORTAL) {
                $this->notifications->notifySubmitted($claim);
            }

            return $claim;
        });
    }

    // ── Private helper ────────────────────────────────────────────────────────────
    private function extractSumInsured(Policy $policy, ?int $riskId = null): float
    {
        $raw   = $policy->raw_payload ?? [];
        $risks = $raw['risks'] ?? [];

        // Old payload format — flat structure with no risks key
        if (empty($risks)) {
            $entry = is_array($raw[0] ?? null) ? $raw[0] : $raw;
            return (float) ($entry['sum_insured'] ?? 0);
        }

        // Fleet claim for a specific risk — use that risk's sum insured
        if ($riskId && isset($risks[$riskId])) {
            return (float) ($risks[$riskId]['sum_insured'] ?? 0);
        }

        // Single risk policy — use the only one
        if (count($risks) === 1) {
            return (float) (reset($risks)['sum_insured'] ?? 0);
        }

        // Fleet with no specific risk targeted — sum all risks
        return (float) collect($risks)->sum(fn($r) => (float) ($r['sum_insured'] ?? 0));
    }

    // Auto-assign to staff member with lowest open claim count in the branch
    public function autoAssign(Claim $claim): void
    {
        $staff = User::where('branch_id', $claim->branch_id)
            ->where('is_active', true)
            ->whereIn('role', [UserRole::STAFF, UserRole::CLAIM_HEAD])
            ->withCount(['assignedClaims as open_claims_count' => function ($query) {
                $query->whereNotIn('status', ClaimStatus::terminal());
            }])
            ->orderBy('open_claims_count', 'asc')
            ->first();

        if (! $staff) {
            return;
        }

        $claim->update([
            'assigned_to' => $staff->id,
            'assigned_by' => null, // system assigned
            'assigned_at' => now(),
            'status'      => ClaimStatus::UNDER_REVIEW,
        ]);

        $this->notifications->notifyUnderReview($claim);

        $this->logActivity(
            $claim,
            null,
            'assigned',
            "Auto-assigned to {$staff->name} based on branch workload.",
            ['assigned_to' => $staff->id, 'method' => 'auto']
        );
    }

    // Manual assign or reassign by a staff member
    public function assign(Claim $claim, User $assignee, User $assignedBy, ?string $note = null): void
    {
        $previousAssignee = $claim->assigned_to;

        $claim->update([
            'assigned_to' => $assignee->id,
            'assigned_by' => $assignedBy->id,
            'assigned_at' => now(),
            'status'      => ClaimStatus::UNDER_REVIEW,
        ]);

        $this->notifications->notifyUnderReview($claim);

        $action = $previousAssignee ? 'reassigned' : 'assigned';

        $this->logActivity(
            $claim,
            $assignedBy,
            $action,
            $note ?? "Claim {$action} to {$assignee->name} by {$assignedBy->name}.",
            [
                'assigned_to'   => $assignee->id,
                'assigned_from' => $previousAssignee,
                'method'        => 'manual',
            ]
        );
    }

    // Update claim status
    public function updateStatus(Claim $claim, string $newStatus, User $updatedBy, ?string $note = null): void
    {
        $oldStatus = $claim->status;

        $claim->update(['status' => $newStatus]);

        $this->logActivity(
            $claim,
            $updatedBy,
            'status_changed',
            $note ?? "Status changed from " . ClaimStatus::labels()[$oldStatus] . " to " . ClaimStatus::labels()[$newStatus],
            ['old_status' => $oldStatus, 'new_status' => $newStatus]
        );
    }

    // Request additional information from customer
    public function requestInfo(Claim $claim, User $requestedBy, string $note): void
    {
        $claim->update(['status' => ClaimStatus::PENDING_INFO]);

        $this->logActivity(
            $claim,
            $requestedBy,
            'info_requested',
            $note,
            ['requested_by' => $requestedBy->id]
        );
    }

    // Update form data (staff completing missing fields)
    public function updateFormData(Claim $claim, array $newData, User $updatedBy, ?string $note = null): void
    {
        $claim->update([
            'form_data' => array_merge($claim->form_data ?? [], $newData),
            'status'    => ClaimStatus::IN_PROGRESS,
        ]);

        $this->logActivity(
            $claim,
            $updatedBy,
            'form_updated',
            $note ?? 'Claim details updated by staff.',
        );
    }

    // Shared activity logger
    private function logActivity(
        Claim $claim,
        ?User $user,
        string $action,
        ?string $note = null,
        array $meta = []
    ): void {
        ClaimActivity::create([
            'claim_id' => $claim->id,
            'user_id'  => $user?->id,
            'action'   => $action,
            'note'     => $note,
            'meta'     => ! empty($meta) ? $meta : null,
        ]);
    }

    // In ClaimService — add this public wrapper
    public function logActivityPublic(Claim $claim, ?User $user, string $action, ?string $note = null, array $meta = []): void
    {
        $this->logActivity($claim, $user, $action, $note, $meta);
    }

    // Resolve branch from policy — fallback to head office
    private function resolveBranch(Policy $policy): int
    {
        return Branch::where('code', 'ACC-HQ')->first()?->id ?? Branch::first()->id;
    }

    /**
     * Store uploaded files and record them in claim_documents.
     * Works for both customer uploads (uploadedBy = null) and staff uploads.
     */
    public function attachDocuments(
        Claim $claim,
        array $files,
        ?User $uploadedBy = null,
        string $type = 'supporting',
        ?Customer $uploadedByCustomer = null
    ): void {
        foreach ($files as $file) {
            // Store under claims/{claim_number}/filename — private disk
            $path = $file->storeAs(
                "claims/{$claim->claim_number}",
                $file->getClientOriginalName(),
                'local'
            );

            ClaimDocument::create([
                'claim_id'                => $claim->id,
                'uploaded_by'             => $uploadedBy?->id,
                'uploaded_by_customer_id' => $uploadedByCustomer?->id,
                'type'                    => $type,
                'original_name'           => $file->getClientOriginalName(),
                'file_path'               => $path,
                'mime_type'               => $file->getMimeType(),
                'file_size'               => $file->getSize(),
            ]);
        }

        $this->logActivity(
            $claim,
            $uploadedBy,
            'documents_uploaded',
            count($files) . ' document(s) uploaded.',
            ['count' => count($files), 'type' => $type]
        );
    }

    public function cancel(Claim $claim, User | Customer $cancelledBy, ?string $note = null): void
    {
        DB::transaction(function () use ($claim, $cancelledBy, $note) {
            $previousAssignee = $claim->assigned_to;
            $previousStatus   = $claim->status;

            $claim->update([
                'status'      => ClaimStatus::CANCELLED,
                'assigned_to' => null,
                'assigned_by' => null,
                'assigned_at' => null,
            ]);

            $actorName = $cancelledBy->name;
            $actorType = $cancelledBy instanceof Customer ? 'customer' : 'staff';

            $this->logActivity(
                $claim,
                $cancelledBy instanceof User ? $cancelledBy : null,
                'cancelled',
                $note ?? "Claim reset to Submitted by {$actorName}.",
                [
                    'cancelled_by'      => $cancelledBy->id,
                    'actor_type'        => $actorType,
                    'previous_status'   => $previousStatus,
                    'previous_assignee' => $previousAssignee,
                ]
            );
        });
    }

    // Staff sends a claim to survey
    public function sendToSurvey(Claim $claim, User $sentBy, ?string $note = null): void
    {
        $claim->update([
            'status'      => ClaimStatus::UNDER_SURVEY,
            'surveyed_at' => now(),
        ]);

        // SMS — notify customer
        $this->notifications->notifySentToSurvey($claim);

        $this->logActivity(
            $claim,
            $sentBy,
            'sent_to_survey',
            $note ?? "Claim sent to survey by {$sentBy->name}.",
            ['sent_by' => $sentBy->id]
        );
    }

    // Assign Surveyor
    public function assignSurveyor(Claim $claim, User $surveyor, ?string $note = null): void
    {
        $previous = $claim->surveyed_by;

        $claim->update([
            'surveyed_by' => $surveyor->id,
            'surveyed_at' => $claim->surveyed_at ?? now(),
        ]);

        $this->logActivity(
            $claim,
            $surveyor,
            'surveyor_assigned',
            $note ?? "Claim self-assigned to {$surveyor->name} for survey.",
            ['assigned_to' => $surveyor->id, 'previous_surveyor' => $previous]
        );
    }

    // Surveyor submits their findings
    public function completeSurvey(Claim $claim, User $surveyor, string $notes): void
    {
        $claim->update([
            'status'              => ClaimStatus::SURVEY_COMPLETED,
            'surveyed_by'         => $surveyor->id,
            'survey_notes'        => $notes,
            'survey_completed_at' => now(),
        ]);

        $this->logActivity(
            $claim,
            $surveyor,
            'survey_completed',
            "Survey completed by {$surveyor->name}.",
            ['surveyor_id' => $surveyor->id]
        );
    }

    // Staff escalates a claim to the committee
    public function sendToCommittee(Claim $claim, User $sentBy, ?string $note = null): void
    {
        $claim->update([
            'status'              => ClaimStatus::COMMITTEE_REVIEW,
            'committee_review_at' => now(),
        ]);

        // SMS — notify customer
        $this->notifications->notifySentToCommittee($claim);

        $this->logActivity(
            $claim,
            $sentBy,
            'sent_to_committee',
            $note ?? "Claim escalated to Claims Committee by {$sentBy->name}.",
            ['sent_by' => $sentBy->id]
        );
    }

    // Committee makes the final call
    public function makeCommitteeDecision(Claim $claim, string $decision, User $decidedBy, ?string $notes = null): void
    {
        if (! in_array($decision, [ClaimStatus::APPROVED, ClaimStatus::REJECTED])) {
            throw new \InvalidArgumentException("Invalid committee decision: {$decision}");
        }

        $claim->update([
            'status'               => $decision,
            'committee_notes'      => $notes,
            'committee_decided_by' => $decidedBy->id,
            'committee_decided_at' => now(),
        ]);

        if ($decision === ClaimStatus::APPROVED) {
            $this->notifications->notifyApproved($claim);
        } else {
            $this->notifications->notifyRejected($claim);
        }

        $this->logActivity(
            $claim,
            $decidedBy,
            'committee_decision',
            $notes ?? "Committee decision: " . ClaimStatus::labels()[$decision] . " by {$decidedBy->name}.",
            ['decision' => $decision, 'decided_by' => $decidedBy->id]
        );
    }

    public function finalize(Claim $claim, User $finalizedBy, ?string $note = null): void
    {
        // Guard — only finalize approved claims when the flow changes
        // if ($claim->status !== ClaimStatus::APPROVED) {
        //     throw new \LogicException("Only approved claims can be finalized.");
        // }

        $claim->update([
            'status'       => ClaimStatus::CLOSED,
            'finalized_by' => $finalizedBy->id,
            'finalized_at' => now(),
        ]);

        $this->logActivity(
            $claim,
            $finalizedBy,
            'finalized',
            $note ?? "Claim processing completed by {$finalizedBy->name}.",
            ['finalized_by' => $finalizedBy->id]
        );

        $this->notifications->notifyFinalized($claim);
    }
}
