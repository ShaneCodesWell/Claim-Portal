<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;
use Illuminate\Support\Facades\Log;
use \Carbon\Carbon;

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

    public function syncFromGlims(string $clientCode, Customer $dbCustomer): array
    {
        $syncedPoliciesMap = [];
        $glimsPolicies     = $this->glims->getPoliciesByClientCode($clientCode);

        if (empty($glimsPolicies)) {
            Log::info('PolicySyncService: No GLIMS policies found', ['client_code' => $clientCode]);
            return $syncedPoliciesMap;
        }

        foreach ($glimsPolicies as $glimsPolicy) {
            $policyNumber = $glimsPolicy['POLICY_NUMBER'] ?? null;
            if (! $policyNumber || isset($syncedPoliciesMap[$policyNumber])) {
                continue;
            }

            // FIX 1: Fetch motor risks and extract vehicle number
            $motorRisks    = $this->glims->getMotorRisks($glimsPolicy['POLICY_SEQUENCE']);
            $vehicleNumber = ! empty($motorRisks)
                ? ((array) $motorRisks[0])['objecth_02_plate_number'] ?? null
                : null;

            // FIX 2: Map status correctly
            $statusCode = $glimsPolicy['POLICY_STATUS'] ?? '';
            $status     = match ((string) $statusCode) {
                '3'     => 'active',
                '4'     => 'cancelled',
                '7'     => 'matured',
                default => 'unknown',
            };

            $dbPolicy = Policy::updateOrCreate(
                [
                    'source'        => 'glims',
                    'policy_number' => $policyNumber,
                ],
                [
                    'customer_id'         => $dbCustomer->id,
                    'insured_name'        => $dbCustomer->name,
                    'external_policy_id'  => $glimsPolicy['POLICY_SEQUENCE'] ?? null,
                    'product_id'          => $glimsPolicy['POLICY_PRODUCT_ID'] ?? null,
                    'business_class_id'   => $glimsPolicy['POLICY_LOB_ID'] ?? null,
                    'product_name'        => $glimsPolicy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
                    'business_class_name' => $glimsPolicy['POLICY_MAIN_CLASS_NAME'] ?? 'Unknown Class',
                    'start_date'          => $glimsPolicy['POLICY_COMMENCEMENT_DATE'] ?? null,
                    'end_date'            => $glimsPolicy['POLICY_EXPIRY_DATE'] ?? null,
                    'effective_date'      => $glimsPolicy['POLICY_EFFECTIVE_DATE'] ?? null,
                    'renewal_date'        => null,
                    'status'              => $status, // FIX 2
                                                      // FIX 1: motor risks now included in raw_payload
                    'raw_payload'         => array_merge($glimsPolicy, [
                        'vehicle_number' => $vehicleNumber,
                        'motor_risks'    => $motorRisks,
                    ]),
                    'last_synced_at'      => now(),
                ]
            );

            $syncedPoliciesMap[$policyNumber] = $this->formatGlimsPolicyForResponse(
                $dbPolicy, $dbCustomer, $glimsPolicy, $vehicleNumber
            );
        }

        return $syncedPoliciesMap;
    }

    // Updated signature to accept vehicleNumber
    private function formatGlimsPolicyForResponse(
        Policy $policy,
        Customer $customer,
        array $glimsPolicy,
        ?string $vehicleNumber = null
    ): array {
        return [
            'policy_id'            => $glimsPolicy['POLICY_SEQUENCE'] ?? null,
            'policy_number'        => $policy->policy_number,
            'insured_name'         => $policy->insured_name,
            'product_id'           => $glimsPolicy['POLICY_PRODUCT_ID'] ?? null,
            'business_class_id'    => $glimsPolicy['POLICY_LOB_ID'] ?? null,
            'product_name'         => $glimsPolicy['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',
            'business_class_name'  => $glimsPolicy['POLICY_MAIN_CLASS_NAME'] ?? 'Unknown Class',
            'lob_name'             => $glimsPolicy['POLICY_LOB_NAME'] ?? 'Unknown LOB',
            'branch_name'          => $glimsPolicy['POLICY_BRANCH_NAME'] ?? 'Unknown Branch',
            'agent_name'           => $glimsPolicy['POLICY_AGENT_NAME'] ?? 'Unknown Agent',
            'policy_start_date'    => $policy->start_date,
            'policy_end_date'      => $policy->end_date,
            'renewal_date'         => null,
            'effective_date'       => $policy->effective_date,
            'vehicle_number'       => $vehicleNumber, // no longer hardcoded null
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
