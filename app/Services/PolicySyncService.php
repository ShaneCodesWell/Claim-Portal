<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PolicySyncService
{
    private GlimsApiService $glims;

    // ── Constructor ───────────────────────────────────────────────────────────
    // NOTE: Inject GlimsApiService (HTTP) instead of the old GlimsService (Oracle).
    // Update your AppServiceProvider / container binding if you had one registered.

    public function __construct(GlimsApiService $glims)
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

        // Resolve product/class names from catalogue, fall back to existing DB record
        $productInfo = $productId ? ($allProducts[$productId] ?? []) : [];

        if (empty($productInfo)) {
            $existing    = Policy::where('external_policy_id', (string) $policyId)->first();
            $productInfo = [
                'name'                => $existing?->product_name,
                'business_class_name' => $existing?->business_class_name,
            ];
        }

        // Extract the first risk's vehicle number (plate) as the primary vehicle_number
        $firstRisk     = collect($risks)->first() ?? [];
        $vehicleNumber = $firstRisk['risk_ref_no'] ?? null;

        $status = ($endDate && Carbon::parse($endDate)->isPast()) ? 'expired' : 'active';

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
                    'risks'             => $risks, // ← full vehicle detail lives here
                    'policy_start_date' => $policyData['policy_start'] ?? null,
                    'policy_end_date'   => $endDate,
                    'effective_date'    => $policyData['effective_start_date'] ?? null,
                    'renewal_date'      => $policyData['renewal_date'] ?? null,
                ],
                'last_synced_at'      => now(),
            ]
        );
    }

    // ── GLIMS sync (rewritten for middleware API payload) ─────────────────────

    /**
     * Sync all policies for a GLIMS customer from the middleware API.
     *
     * The middleware returns flat rows — one per policy (or one per vehicle for fleet).
     * GlimsApiService::getPoliciesByClientCode() groups these into one array entry
     * per policy_number, with a 'risks' key holding all vehicles.
     *
     * Raw payload shape stored in DB:
     * {
     *   "POLICY_NUMBER": "P-1015-512-2021-000119",
     *   "POLICY_LOB_NAME": "MOTOR",
     *   "POLICY_PRODUCT_NAME": "MOTOR COMPREHENSIVE",
     *   "POLICY_START_DATE": "2024-02-13",
     *   "POLICY_EXPIRY_DATE": "2025-02-12",
     *   "POLICY_TOTAL_PREMIUM": 482,
     *   "POLICY_TOTAL_SI": 0,
     *   "POLICY_CURRENCY": "GHC",
     *   "is_fleet": false,
     *   "risks": [
     *     { "risk_ref_no": "GR 8080 U", "sum_insured": 0, "total_premium": 482, ... }
     *   ]
     * }
     */
    public function syncFromGlims(string $clientCode, Customer $dbCustomer): array
    {
        $syncedPoliciesMap = [];

        $glimsPolicies = $this->glims->getPoliciesByClientCode($clientCode);

        if (empty($glimsPolicies)) {
            Log::info('PolicySyncService: No GLIMS policies found via API', [
                'client_code' => $clientCode,
            ]);
            return $syncedPoliciesMap;
        }

        foreach ($glimsPolicies as $policy) {
            $policyNumber = $policy['POLICY_NUMBER'] ?? null;

            if (! $policyNumber || isset($syncedPoliciesMap[$policyNumber])) {
                continue;
            }

            $status = $this->resolveStatus($policy);

            // Build the raw_payload — everything the frontend and PolicyResource need
            $rawPayload = array_merge($policy, [
                // Explicit top-level keys for fast access without digging into nested arrays
                'source'       => 'glims',
                'status_label' => $status,
            ]);

            $dbPolicy = Policy::updateOrCreate(
                [
                    'source'        => 'glims',
                    'policy_number' => $policyNumber,
                ],
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
                    'renewal_date'        => null, // not in middleware response
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

    // ── Private: Status resolution ────────────────────────────────────────────

    /**
     * Derive a status string from the grouped policy data.
     * The middleware only returns active policies (filtered server-side),
     * but we cross-check expiry date to catch anything that slipped through.
     */
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

    // ── Private: Response formatters ─────────────────────────────────────────

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
