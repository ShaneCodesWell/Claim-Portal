<?php

namespace App\Traits;

use App\Models\ClaimDraft;
use App\Models\Customer;
use App\Models\Policy;

trait MergesClaimDraftData
{
    /**
     * Merge saved draft form_data over the defaults pulled from policy/customer data.
     * Draft values win, since they reflect what the customer already typed in.
     */
    protected function mergeDraftFormData(Customer $customer, Policy $policy, string $claimType, array $formData): array
    {
        $customerIds = $customer->resolvedCustomerIds();

        $draft = ClaimDraft::with('documents')
            ->whereIn('customer_id', $customerIds)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $claimType)
            ->first();

        if ($draft) {
            $formData = array_merge($formData, $draft->form_data ?? []);
        }

        return $formData;
    }

    /**
     * Same lookup, but returns the draft itself rather than merged form data —
     * needed by views that render draft documents (e.g. Motor's document list).
     */
    protected function findDraftFor(Customer $customer, Policy $policy, string $claimType): ?ClaimDraft
    {
        $customerIds = $customer->resolvedCustomerIds();

        return ClaimDraft::with('documents')
            ->whereIn('customer_id', $customerIds)
            ->where('policy_id', $policy->id)
            ->where('claim_type', $claimType)
            ->first();
    }
}
