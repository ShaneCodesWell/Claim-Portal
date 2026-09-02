<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Agent;
use App\Models\Policy;
use App\Models\GenovaBusinessClass;
use App\Models\GenovaProduct;
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
     * Sync one policy entry from Genova's agent policy-search response.
     * Each entry already contains customer + policy + risks — no rich-fetch needed.
     */
    public function syncAgentPolicyFromGenova(array $entry, Agent $agent): void
    {
        $policyData   = $entry['policy'] ?? [];
        $customerData = $entry['customer'] ?? [];
        $risksData    = $entry['risks'] ?? [];

        $policyNo = $policyData['policy_no'] ?? null;
        if (! $policyNo) {
            return;
        }

        // Find-or-create the customer this policy belongs to.
        // Genova's ins_code is the closest equivalent to GLIMS's customer_code.
        $customer     = null;
        $customerCode = $customerData['ins_code'] ?? null;

        if ($customerCode) {
            $customer = Customer::firstOrCreate(
                ['genova_customer_code' => $customerCode],
                [
                    'name'    => $customerData['ins_name'] ?? 'Unknown',
                    'phone'   => $customerData['cust_phone'] ?? null,
                    'email'   => $customerData['ins_email'] ?: null,
                    'sources' => ['genova'],
                ]
            );

            // The agent sync's embedded customer object already carries rich
            // detail (dob, occupation, nationality, postal address, id number)
            // — no extra API call needed, unlike GLIMS. Reshape field names to
            // match what refreshCustomerFromGenova() expects, then populate
            // raw_payload['genova'] the same way the customer-login path does.
            if (! empty($customerData)) {
                $this->refreshCustomerFromGenova($customer, $this->normaliseAgentGenovaCustomerData($customerData));
            }
        }

        $endDate = $policyData['policy_end'] ?? null;
        $status  = ($endDate && Carbon::parse($endDate)->isPast()) ? 'expired' : 'active';

        // Business class + product names — resolved from the local cache,
        // populated by RefreshGenovaProductCacheJob. No extra API calls per policy.
        $businessClassId = $policyData['esu_main_product_id'] ?? null;
        $productId        = $policyData['esu_product_id'] ?? null;

        $businessClass = $businessClassId ? GenovaBusinessClass::find($businessClassId) : null;
        $product       = $productId ? GenovaProduct::find($productId) : null;

        $risks = collect($risksData)->values()->map(function ($risk) {
            return [
                'risk_ref_no'            => $risk['risk_ref_no'] ?? null,
                'vehicle_make'           => $risk['vehicle_make'] ?? null,
                'vehicle_model'          => $risk['vehicle_model'] ?? null,
                'vehicle_yr_manufacture' => $risk['vehicle_yr_manufacture'] ?? null,
                'vehicle_chassis_no'     => $risk['vehicle_chassis_no'] ?? null,
                'vehicle_colour'         => $risk['vehicle_colour'] ?? null,
                'vehicle_body_type'      => $risk['vehicle_body_type'] ?? null,
                'sum_insured'            => $risk['sum_insured'] ?? null,
                'total_premium'          => $risk['total_premium'] ?? null,
                '_raw'                   => $risk,
            ];
        })->toArray();

        Policy::updateOrCreate(
            ['source' => 'genova', 'policy_number' => $policyNo],
            [
                'customer_id'         => $customer?->id,
                'agent_id' => $agent->portfolioAgentId(),
                'insured_name'        => $customerData['ins_name'] ?? $policyData['insured_name'] ?? null,
                'external_policy_id'  => (string) ($policyData['id'] ?? ''),
                'product_id'          => $productId,
                'product_name'        => $product->name ?? 'Unknown Product',
                'business_class_id'   => $businessClassId,
                'business_class_name' => $businessClass->name ?? 'Unknown Class',
                'start_date'          => $policyData['policy_start'] ?? null,
                'end_date'            => $endDate,
                'renewal_date'        => $policyData['renewal_date'] ?? null,
                'effective_date'      => $policyData['effective_start_date'] ?? $policyData['policy_start'] ?? null,
                'status'              => $status,
                'raw_payload'         => [
                    'policy'   => $policyData,
                    'customer' => $customerData,
                    'risks'    => $risks,
                    'is_fleet' => count($risks) > 1,
                ],
                'last_synced_at'      => now(),
            ]
        );
    }

    /**
     * Reshape a Genova agent-sync embedded customer object (ins_* prefixed
     * fields, from the policy-search response) into the flatter shape
     * refreshCustomerFromGenova() expects (matching the customer-search
     * endpoint's field names, e.g. `name`, `email`, `phone_number`, `dob`).
     *
     * Two different Genova endpoints describe a customer with two different
     * field naming conventions — this is the translation layer between them
     * so both sync paths (customer-login vs agent-sync) can share one
     * refreshCustomerFromGenova() implementation and store a consistent shape
     * under Customer::raw_payload['genova'].
     */
    private function normaliseAgentGenovaCustomerData(array $customerData): array
    {
        return [
            'code'           => $customerData['ins_code'] ?? null,
            'name'           => $customerData['ins_name'] ?? null,
            'email'          => $customerData['ins_email'] ?? null,
            'phone_number'   => $customerData['cust_phone'] ?? null,
            'dob'            => $customerData['ins_dob'] ?? null,
            'address'        => $customerData['ins_postaladdress'] ?? $customerData['ins_address'] ?? null,
            'occupation'     => $customerData['ins_occupation'] ?? null,
            'nationality'    => $customerData['ins_nationality'] ?? null,
            'id_number'      => $customerData['identification_number'] ?? null,
            'id_type'        => $customerData['identification_type'] ?? null,
            'gender'         => $customerData['ins_gender'] ?? null,
            'marital_status' => $customerData['ins_maritalstatus'] ?? null,
            // Keep the untouched original for anything not mapped above
            '_raw'           => $customerData,
        ];
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
     * Sync one grouped-and-enriched GLIMS policy from the agent sync job.
     * Expects the same shape produced by GlimsApiService::groupPolicyRows(),
     * with 'risks' already replaced by rich detail from getRisksForPolicy().
     *
     * Mirrors syncFromGlimsRich() but additionally sets agent_id, since these
     * policies come from an agent's portfolio rather than a logged-in customer.
     */
    public function syncAgentPolicyFromGlims(array $policy, Agent $agent): void
    {
        $policyNumber = $policy['POLICY_NUMBER'] ?? null;

        if (! $policyNumber) {
            return;
        }

        // Find-or-create the customer this policy belongs to.
        $customerCode = (string) ($policy['CUSTOMER_CODE'] ?? '');
        $customer     = null;

        if ($customerCode) {
            $fullName = trim(implode(' ', array_filter([
                $policy['CUSTOMER_FIRST_NAME'] ?? null,
                $policy['CUSTOMER_OTHER_NAMES'] ?? null,
                $policy['CUSTOMER_FAMILY_NAME'] ?? null,
            ])));

            $customer = Customer::firstOrCreate(
                ['glims_customer_code' => $customerCode],
                [
                    'name'    => $fullName ?: 'Unknown',
                    'phone'   => null,
                    'email'   => null,
                    'sources' => ['glims'],
                ]
            );
        }

        $status     = $this->resolveStatus($policy);
        $rawPayload = array_merge($policy, [
            'source'       => 'glims',
            'status_label' => $status,
        ]);

        Policy::updateOrCreate(
            ['source' => 'glims', 'policy_number' => $policyNumber],
            [
                'customer_id'         => $customer?->id,
                'agent_id' => $agent->portfolioAgentId(),
                'insured_name'        => $customer->name ?? null,
                'external_policy_id'  => (string) ($policy['POLICY_ID'] ?? $policyNumber),
                'product_id'          => $policy['POLICY_PRODUCT_CODE'] ?? null,
                'product_name'        => $policy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
                'business_class_id'   => null, // not in middleware — use LOB name instead
                'business_class_name' => $policy['POLICY_LOB_NAME'] ?? 'Unknown Class',
                'start_date'          => $policy['POLICY_START_DATE'] ?? null,
                'end_date'            => $policy['POLICY_EXPIRY_DATE'] ?? null,
                'effective_date'      => $policy['POLICY_ISSUE_DATE'] ?? null,
                'renewal_date'        => $policy['POLICY_EXPIRY_DATE']
                    ? Carbon::parse($policy['POLICY_EXPIRY_DATE'])->addDay()->toDateString()
                    : null,
                'status'              => $status,
                'raw_payload'         => $rawPayload,
                'last_synced_at'      => now(),
            ]
        );
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
            'customer_code'        => $customer->glims_customer_code,
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
            'customer_code'       => $customer->genova_customer_code ?? $customer->glims_customer_code,
            'customer_phone'      => $customer->phone,
            'customer_email'      => $customer->email,
            'source'              => $policy->source,
        ];
    }
}
