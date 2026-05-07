<?php
namespace App\Services;

use App\Models\Customer;
use App\Models\Policy;
use Illuminate\Support\Facades\Log;

class GlimsSyncService
{
    private GlimsService $glims;

    public function __construct(GlimsService $glims)
    {
        $this->glims = $glims;
    }

    /**
     * Full sync for a single customer — upserts customer + all their policies.
     * Called from the Artisan command or on-demand.
     *
     * @return array  Map of policy_number → policy data (same shape DashboardController expects)
     */
    public function syncCustomer(array $glimsCustomer): array
    {
        $clientCode = $glimsCustomer['CLIENT_CODE'];

        // ── 1. Upsert the customer into Postgres ──────────────────────────

        // Merge 'glims' into the sources array without overwriting existing sources
        // e.g. a customer already synced from Genova will end up with ["genova", "glims"]
        $existing = Customer::where('external_customer_code', $clientCode)->first();
        $sources  = $existing ? ($existing->sources ?? []) : [];

        if (! in_array('glims', $sources)) {
            $sources[] = 'glims';
        }

        $dbCustomer = Customer::updateOrCreate(
            ['external_customer_code' => $clientCode],
            [
                'sources'        => $sources,
                'name'           => trim(
                    ($glimsCustomer['CLIENT_FIRST_NAME'] ?? '') . ' ' .
                    ($glimsCustomer['CLIENT_MIDDLE_NAME'] ?? '') . ' ' .
                    ($glimsCustomer['CLIENT_FAMILY_NAME'] ?? '')
                ),
                'phone'          => $glimsCustomer['CLIENT_HOME_MOBILE'] ?? $glimsCustomer['CLIENT_HOME_TEL'] ?? null,
                'email'          => $glimsCustomer['CLIENT_HOME_EMAIL'] ?? null,
                'last_synced_at' => now(),
            ]
        );

        // ── 2. Fetch & upsert policies ────────────────────────────────────
        $rawPolicies = $this->glims->getPoliciesByClientCode($clientCode);
        $syncedMap   = [];

        foreach ($rawPolicies as $raw) {
            $raw = (array) $raw;

            // TODO: re-enable once we map POLICY_STATUS_REASON codes
            // if (in_array($raw['POLICY_STATUS_REASON'] ?? '', ['CANCELLED', 'LAPSED'])) {
            //     continue;
            // }

            // Pull motor risks to get the vehicle number (if motor policy)
            $motorRisks    = $this->glims->getMotorRisks($raw['POLICY_SEQUENCE']);
            $vehicleNumber = ! empty($motorRisks)
                ? ((array) $motorRisks[0])['CAR_NO'] ?? null
                : null;

            $policy = Policy::updateOrCreate(
                ['external_policy_id' => (string) $raw['POLICY_SEQUENCE']],
                [
                    'customer_id'         => $dbCustomer->id,
                    'source'              => 'glims',
                    'policy_number'       => $raw['POLICY_NUMBER'],
                    'insured_name'        => $dbCustomer->name,
                    'business_class_id'   => $raw['POLICY_MAIN_CLASS'] ?? null,
                    'business_class_name' => $raw['POLICY_MAIN_CLASS'] ?? null,
                    'product_id'          => $raw['POLICY_PRODUCT'] ?? null,
                    'product_name'        => $raw['POLICY_PRODUCT'] ?? null,
                    'start_date'          => $raw['POLICY_COMMENCEMENT_DATE'],
                    'end_date'            => $raw['POLICY_EXPIRY_DATE'],
                    'effective_date'      => $raw['POLICY_EFFECTIVE_DATE'],
                    'renewal_date'        => null, // not in UW1_POLICY — can derive later
                    'status'              => $raw['POLICY_STATUS_REASON'] ?? null,
                    'raw_payload'         => $raw,
                    'last_synced_at'      => now(),
                ]
            );

            $syncedMap[$raw['POLICY_NUMBER']] = [
                'policy_id'           => $policy->external_policy_id,
                'policy_number'       => $policy->policy_number,
                'product_id'          => $policy->product_id,
                'product_name'        => $policy->product_name,
                'business_class_id'   => $policy->business_class_id,
                'business_class_name' => $policy->business_class_name,
                'policy_start_date'   => $policy->start_date,
                'policy_end_date'     => $policy->end_date,
                'renewal_date'        => $policy->renewal_date,
                'effective_date'      => $policy->effective_date,
                'status'              => $policy->status,
                'source'              => 'glims',
                'vehicle_number'      => $vehicleNumber,
                'customer_name'       => $dbCustomer->name,
                'customer_code'       => $dbCustomer->external_customer_code,
                'customer_phone'      => $dbCustomer->phone,
                'customer_email'      => $dbCustomer->email,
            ];

            Log::info('GLIMS policy synced', [
                'policy_number'   => $raw['POLICY_NUMBER'],
                'policy_sequence' => $raw['POLICY_SEQUENCE'],
                'customer_code'   => $clientCode,
            ]);
        }

        return $syncedMap;
    }

    /**
     * Oracle dates come back as Carbon or formatted strings depending on the driver.
     * This normalises them safely to Y-m-d.
     */
    private function parseDate($value): ?string
    {
        if (! $value) {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
