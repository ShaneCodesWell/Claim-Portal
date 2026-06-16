<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PolicySyncService
{
    private GlimsApiService $glims;

    public function __construct(GlimsApiService $glims)
    {
        $this->glims = $glims;
    }

    // Genova sync 
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
                ['source' => 'genova', 'policy_number' => $policyNumber],
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

    /**
     * Sync a single policy from the rich policy-search response.
     * Stores the full risks/vehicle data in raw_payload.
     */
    public function syncFromGenovaRich(array $richData, array $allProducts, Customer $customer): void
    {
        $policyData = $richData['policy'] ?? [];
        $risks      = $richData['risks'] ?? [];
        $policyId   = $richData['id'] ?? null;

        if (! $policyId || empty($policyData)) {
            return;
        }

        $policyNo    = $policyData['policy_no'] ?? null;
        $productId   = $policyData['esu_product_id'] ?? null;
        $mainClassId = $policyData['esu_main_product_id'] ?? null;
        $endDate     = $policyData['policy_end'] ?? null;

        if (! $policyNo) {
            return;
        }

        $productInfo = $productId ? ($allProducts[$productId] ?? []) : [];

        if (empty($productInfo)) {
            $existing    = Policy::where('external_policy_id', (string) $policyId)->first();
            $productInfo = [
                'name'                => $existing?->product_name,
                'business_class_name' => $existing?->business_class_name,
            ];
        }

        $firstRisk     = collect($risks)->first() ?? [];
        $vehicleNumber = $firstRisk['risk_ref_no'] ?? null;
        $status        = ($endDate && Carbon::parse($endDate)->isPast()) ? 'expired' : 'active';

        Policy::updateOrCreate(
            ['external_policy_id' => (string) $policyId],
            [
                'customer_id'         => $customer->id,
                'policy_number'       => $policyNo,
                'product_id'          => $productId,
                'product_name'        => $productInfo['name'] ?? null,
                'business_class_id'   => $mainClassId,
                'business_class_name' => $productInfo['business_class_name'] ?? null,
                'start_date'          => $policyData['policy_start'] ?? null,
                'end_date'            => $endDate,
                'renewal_date'        => $policyData['renewal_date'] ?? null,
                'effective_date'      => $policyData['effective_start_date'] ?? null,
                'status'              => $status,
                'source'              => 'genova',
                'raw_payload'         => [
                    'policy_number'     => $policyNo,
                    'policy_id'         => $policyId,
                    'product_id'        => $productId,
                    'vehicle_number'    => $vehicleNumber,
                    'risks'             => $risks,
                    'policy_start_date' => $policyData['policy_start'] ?? null,
                    'policy_end_date'   => $endDate,
                    'effective_date'    => $policyData['effective_start_date'] ?? null,
                    'renewal_date'      => $policyData['renewal_date'] ?? null,
                ],
                'last_synced_at'      => now(),
            ]
        );
    }

    /**
     * Refresh a Customer record from a Genova customer-search API response.
     * Stores data under raw_payload['genova'] — never overwrites raw_payload['glims'].
     */
    public function refreshCustomerFromGenova(Customer $customer, array $genovaContent): void
    {
        try {
            $payloadToStore = array_merge(
                array_diff_key($genovaContent, ['policies' => null]),
                ['_synced_from' => 'genova', '_synced_at' => now()->toDateTimeString()]
            );

            $existing = $customer->raw_payload ?? [];
            $merged   = array_merge($existing, ['genova' => $payloadToStore]);

            $updates = ['raw_payload' => $merged];

            if (! empty($genovaContent['name'])) {
                $updates['name'] = $genovaContent['name'];
            }
            if (! empty($genovaContent['email'])) {
                $updates['email'] = $genovaContent['email'];
            }
            if (! empty($genovaContent['phone_number'])) {
                $updates['phone'] = $genovaContent['phone_number'];
            }

            $sources = $customer->sources ?? [];
            if (! in_array('genova', $sources)) {
                $sources[]          = 'genova';
                $updates['sources'] = $sources;
            }

            $customer->update($updates);

        } catch (\Exception $e) {
            Log::warning('PolicySyncService: refreshCustomerFromGenova failed', [
                'customer_id' => $customer->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    // GLIMS sync
    /**
     * Sync GLIMS policies that already have rich risk detail merged in.
     * Called by SyncCustomerPoliciesJob after it has fetched policy details
     * and merged them into each policy's 'risks' key.
     *
     * This replaces the old syncFromGlims() which had no rich detail.
     */
    public function syncFromGlimsRich(array $policies, Customer $dbCustomer): array
    {
        $syncedPoliciesMap = [];

        foreach ($policies as $policy) {
            $policyNumber = $policy['POLICY_NUMBER'] ?? null;

            if (! $policyNumber || isset($syncedPoliciesMap[$policyNumber])) {
                continue;
            }

            $status     = $this->resolveStatus($policy);
            $rawPayload = array_merge($policy, [
                'source'       => 'glims',
                'status_label' => $status,
            ]);

            $dbPolicy = Policy::updateOrCreate(
                ['source' => 'glims', 'policy_number' => $policyNumber],
                [
                    'customer_id'         => $dbCustomer->id,
                    'insured_name'        => $dbCustomer->name,
                    'external_policy_id'  => (string) ($policy['POLICY_ID'] ?? $policyNumber),
                    'product_id'          => $policy['POLICY_PRODUCT_CODE'] ?? null,
                    'product_name'        => $policy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
                    'business_class_id'   => null, // not in middleware — use LOB name instead
                    'business_class_name' => $policy['POLICY_LOB_NAME'] ?? 'Unknown Class',
                    'start_date'          => $policy['POLICY_START_DATE'] ?? null,
                    'end_date'            => $policy['POLICY_EXPIRY_DATE'] ?? null,
                    'effective_date'      => $policy['POLICY_ISSUE_DATE'] ?? null,
                    'renewal_date'        => $policy['POLICY_EXPIRY_DATE'] ? Carbon::parse($policy['POLICY_EXPIRY_DATE'])->addDay()->toDateString() : null,
                    'status'              => $status,
                    'raw_payload'         => $rawPayload,
                    'last_synced_at'      => now(),
                ]
            );

            $syncedPoliciesMap[$policyNumber] = $this->formatGlimsPolicyForResponse(
                $dbPolicy,
                $dbCustomer,
                $policy
            );
        }

        return $syncedPoliciesMap;
    }

    /**
     * Refresh a Customer record from a raw GLIMS customer search result row.
     * Stored under raw_payload['glims'] — never overwrites raw_payload['genova'].
     */
    public function refreshCustomerFromGlimsRow(Customer $customer, array $glimsRow): void
    {
        try {
            $firstName  = $glimsRow['first_name'] ?? null;
            $otherNames = $glimsRow['other_names'] ?? null;
            $familyName = $glimsRow['family_name'] ?? null;

            $fullName = trim(implode(' ', array_filter([
                $firstName,
                $otherNames,
                $familyName,
            ])));

            $payloadToStore = array_merge($glimsRow, [
                '_synced_from' => 'glims',
                '_synced_at'   => now()->toDateTimeString(),
            ]);

            $existing = $customer->raw_payload ?? [];
            $merged   = array_merge($existing, ['glims' => $payloadToStore]);

            $updates = ['raw_payload' => $merged];

            // Update core fields — prefer non-empty API value over existing blank
            if (! empty($fullName) && empty($customer->name)) {
                $updates['name'] = $fullName;
            }
            if (! empty($glimsRow['mobile_number']) && empty($customer->phone)) {
                $updates['phone'] = $glimsRow['mobile_number'];
            }
            if (! empty($glimsRow['email']) && empty($customer->email)) {
                $updates['email'] = $glimsRow['email'];
            }

            // Ensure 'glims' is in sources
            $sources = $customer->sources ?? [];
            if (! in_array('glims', $sources)) {
                $sources[]          = 'glims';
                $updates['sources'] = $sources;
            }

            $customer->update($updates);

        } catch (\Exception $e) {
            Log::warning('PolicySyncService: refreshCustomerFromGlimsRow failed', [
                'customer_id' => $customer->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    // Private helpers
    private function resolveStatus(array $policy): string
    {
        $expiry = $policy['POLICY_EXPIRY_DATE'] ?? null;

        if ($expiry) {
            try {
                return Carbon::parse($expiry)->isPast() ? 'expired' : 'active';
            } catch (\Exception $e) {
                // Malformed date — default to active since the API filters for active
            }
        }

        return 'active';
    }

    // Private: Response formatters
    private function formatGlimsPolicyForResponse(
        Policy $policy,
        Customer $customer,
        array $glimsPolicy
    ): array {
        $risks   = $glimsPolicy['risks'] ?? [];
        $isFleet = $glimsPolicy['is_fleet'] ?? (count($risks) > 1);

        // Vehicle number display: FLEET for multiple, plate for single, null for non-motor
        $vehicleNumber = $isFleet
            ? 'FLEET'
            : ($risks[0]['risk_ref_no'] ?? null);

        return [
            'policy_id'            => $glimsPolicy['POLICY_ID'] ?? null,
            'policy_number'        => $policy->policy_number,
            'insured_name'         => $policy->insured_name,
            'product_name'         => $glimsPolicy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
            'business_class_name'  => $glimsPolicy['POLICY_LOB_NAME'] ?? 'Unknown Class',
            'branch_name'          => $glimsPolicy['POLICY_BRANCH_NAME'] ?? null,
            'agent_name'           => $glimsPolicy['POLICY_AGENT_NAME'] ?? null,
            'policy_start_date'    => $policy->start_date,
            'policy_end_date'      => $policy->end_date,
            'renewal_date'         => null,
            'effective_date'       => $policy->effective_date,
            'vehicle_number'       => $vehicleNumber,
            'is_fleet'             => $isFleet,
            'risks'                => $risks,
            'customer_name'        => $customer->name,
            'customer_code'        => $customer->external_customer_code,
            'customer_phone'       => $customer->phone,
            'customer_email'       => $customer->email,
            'source'               => 'glims',
            'status'               => $policy->status,
            'policy_currency'      => $glimsPolicy['POLICY_CURRENCY'] ?? null,
            'policy_total_premium' => $glimsPolicy['POLICY_TOTAL_PREMIUM'] ?? null,
            'policy_total_si'      => $glimsPolicy['POLICY_TOTAL_SI'] ?? null,
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
