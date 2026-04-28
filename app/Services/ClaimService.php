<?php
namespace App\Services;

use App\Models\Claim;
use App\Models\ClaimActivity;
use App\Models\ClaimDocument;
use App\Models\Customer;
use App\Models\Policy;
use App\Models\Branch;
use App\Models\User;
use App\Enums\ClaimStatus;
use App\Enums\ClaimSource;
use App\Enums\UserRole;
use Illuminate\Support\Facades\DB;

class ClaimService
{
    // Register a new claim
    public function register(
        Customer $customer,
        Policy $policy,
        string $claimType,
        array $formData,
        string $source = ClaimSource::CUSTOMER_PORTAL
    ): Claim {
        return DB::transaction(function () use ($customer, $policy, $claimType, $formData, $source) {

            $claim = Claim::create([
                'claim_number' => Claim::generateClaimNumber(),
                'customer_id'  => $customer->id,
                'policy_id'    => $policy->id,
                'branch_id'    => $policy->branch_id ?? $this->resolveBranch($policy),
                'claim_type'   => $claimType,
                'source'       => $source,
                'status'       => ClaimStatus::SUBMITTED,
                'form_data'    => $formData,
                'submitted_at' => now(),
            ]);

            // Log submission activity
            $this->logActivity($claim, null, 'submitted', 'Claim submitted via ' . ClaimSource::labels()[$source]);

            // Auto-assign based on branch
            $this->autoAssign($claim);

            return $claim;
        });
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

    // Resolve branch from policy — fallback to head office
    private function resolveBranch(Policy $policy): int
    {
        return Branch::where('code', 'ACC-HQ')->first()?->id ?? Branch::first()->id;
    }
}
