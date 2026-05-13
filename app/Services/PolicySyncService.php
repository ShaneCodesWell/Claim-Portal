<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;
use Illuminate\Support\Facades\Log;

class PolicySyncService
{
    private GlimsService $glims;

    public function __construct(GlimsService $glims)
    {
        $this->glims = $glims;
    }

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

    public function syncFromGlims(string $clientCode, Customer $dbCustomer): array
    {
        $syncedPoliciesMap = [];

        $glimsPolicies = $this->glims->getPoliciesByClientCode($clientCode);

        if (empty($glimsPolicies)) {
            Log::info('PolicySyncService: No GLIMS policies found', ['client_code' => $clientCode]);
            return $syncedPoliciesMap;
        }

        foreach ($glimsPolicies as $glimsPolicy) {
            $policyNumber = $glimsPolicy['POLICY_NUMBER'] ?? null;

            if (! $policyNumber) {
                continue;
            }

            if (isset($syncedPoliciesMap[$policyNumber])) {
                continue;
            }

            $dbPolicy = Policy::updateOrCreate(
                [
                    'source'        => 'glims',
                    'policy_number' => $policyNumber,
                ],
                [
                    'customer_id'         => $dbCustomer->id,
                    'insured_name'        => $dbCustomer->name,
                    'external_policy_id'  => $glimsPolicy['POLICY_SEQUENCE'] ?? null,

                    // Store raw IDs for internal reference/filtering
                    'product_id'          => $glimsPolicy['POLICY_PRODUCT_ID'] ?? null,
                    'business_class_id'   => $glimsPolicy['POLICY_LOB_ID'] ?? null,

                    // Store resolved human-readable names for display
                    'product_name'        => $glimsPolicy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
                    'business_class_name' => $glimsPolicy['POLICY_MAIN_CLASS_NAME'] ?? 'Unknown Class',

                    'start_date'          => $glimsPolicy['POLICY_COMMENCEMENT_DATE'] ?? null,
                    'end_date'            => $glimsPolicy['POLICY_EXPIRY_DATE'] ?? null,
                    'effective_date'      => $glimsPolicy['POLICY_EFFECTIVE_DATE'] ?? null,
                    'renewal_date'        => null,
                    'raw_payload'         => $glimsPolicy,
                    'last_synced_at'      => now(),
                ]
            );

            Log::info('PolicySyncService: GLIMS policy synced', [
                'policy_number' => $policyNumber,
                'customer_id'   => $dbCustomer->id,
            ]);

            $syncedPoliciesMap[$policyNumber] = $this->formatGlimsPolicyForResponse($dbPolicy, $dbCustomer, $glimsPolicy);
        }

        return $syncedPoliciesMap;
    }

    private function formatGlimsPolicyForResponse(Policy $policy, Customer $customer, array $glimsPolicy): array
    {
        return [
            'policy_id'            => $glimsPolicy['POLICY_SEQUENCE'] ?? null,
            'policy_number'        => $policy->policy_number,
            'insured_name'         => $policy->insured_name,

            // IDs for internal use
            'product_id'           => $glimsPolicy['POLICY_PRODUCT_ID'] ?? null,
            'business_class_id'    => $glimsPolicy['POLICY_LOB_ID'] ?? null,

            // Human-readable names for display
            'product_name'         => $glimsPolicy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
            'business_class_name'  => $glimsPolicy['POLICY_MAIN_CLASS_NAME'] ?? 'Unknown Class',
            'lob_name'             => $glimsPolicy['POLICY_LOB_NAME'] ?? 'Unknown LOB',
            'branch_name'          => $glimsPolicy['POLICY_BRANCH_NAME'] ?? 'Unknown Branch',
            'agent_name'           => $glimsPolicy['POLICY_AGENT_NAME'] ?? 'Unknown Agent',
            

            'policy_start_date'    => $policy->start_date,
            'policy_end_date'      => $policy->end_date,
            'renewal_date'         => null,
            'effective_date'       => $policy->effective_date,
            'vehicle_number'       => null,

            'customer_name'        => $customer->name,
            'customer_code'        => $customer->external_customer_code,
            'customer_phone'       => $customer->phone,
            'customer_email'       => $customer->email,

            'source'               => 'glims',
            'policy_status'        => $glimsPolicy['POLICY_STATUS'] ?? null,
            'policy_currency'      => $glimsPolicy['POLICY_CURRENCY'] ?? null,
            'policy_total_premium' => $glimsPolicy['POLICY_TOTAL_PREMIUM'] ?? null,
        ];
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
