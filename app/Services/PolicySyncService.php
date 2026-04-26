<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;

class PolicySyncService
{
    public function syncFromGenova(array $customerInfo, array $allProducts, Customer $dbCustomer): array
    {
        $syncedPoliciesMap = [];

        if (! isset($customerInfo['policies']) || ! is_array($customerInfo['policies'])) {
            return $syncedPoliciesMap;
        }

        $groupedByNumber = collect($customerInfo['policies'])->groupBy('policy_number');

        foreach ($groupedByNumber as $policyNumber => $subPolicies) {
            // Skip if already processed in this sync run
            if (isset($syncedPoliciesMap[$policyNumber])) {
                continue;
            }

            $firstPolicy = $subPolicies->first();
            $productId   = $firstPolicy['product_id'] ?? null;

            $dbPolicy = Policy::updateOrCreate(
                [
                    'source'        => 'genova',
                    'policy_number' => $policyNumber,
                ],
                [
                    'customer_id'         => $dbCustomer->id,
                    'insured_name'        => $customerInfo['name'],
                    'external_policy_id'  => $firstPolicy['policy_id'] ?? null,
                    'product_id'          => $productId,
                    'product_name'        => $allProducts[$productId]['name'] ?? 'Unknown Product',
                    'business_class_id'   => $allProducts[$productId]['business_class_id'] ?? null,
                    'business_class_name' => $allProducts[$productId]['business_class_name'] ?? 'Unknown Class',
                    'start_date'          => $firstPolicy['policy_start_date'] ?? null,
                    'end_date'            => $firstPolicy['policy_end_date'] ?? null,
                    'effective_date'      => $firstPolicy['effective_date'] ?? null,
                    'renewal_date'        => $firstPolicy['renewal_date'] ?? null,
                    'raw_payload'         => $subPolicies->values()->toArray(),
                    'last_synced_at'      => now(),
                ]
            );

            $syncedPoliciesMap[$policyNumber] = $this->formatPolicyForResponse($dbPolicy, $dbCustomer, $firstPolicy);
        }

        return $syncedPoliciesMap;
    }

    public function syncFromGlims(): array
    {
        // Ready for when GLIMS access is figured out
        return [];
    }

    private function formatPolicyForResponse(Policy $policy, Customer $customer, array $rawPolicy): array
    {
        return [
            'policy_id'           => $policy->external_policy_id,
            'policy_number'       => $policy->policy_number,
            'insured_name'        => $policy->insured_name,
            'product_id'          => $policy->product_id,
            'product_name'        => $policy->product_name,
            'business_class_id'   => $policy->business_class_id,
            'business_class_name' => $policy->business_class_name,
            'policy_start_date'   => $policy->start_date,
            'policy_end_date'     => $policy->end_date,
            'renewal_date'        => $policy->renewal_date,
            'effective_date'      => $policy->effective_date,
            'vehicle_number'      => $rawPolicy['vehicle_number'] ?? null,
            'customer_name'       => $customer->name,
            'customer_code'       => $customer->external_customer_code,
            'customer_phone'      => $customer->phone,
            'customer_email'      => $customer->email,
            'source'              => $policy->source,
        ];
    }
}
