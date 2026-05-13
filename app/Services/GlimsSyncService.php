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

    // Add this at the top of syncCustomer() or as a private method
    private function mapGlimsStatus(string $statusCode): string
    {
        return match ((string) $statusCode) {
            '0'     => 'quote',
            '1'     => 'proposal',
            '2'     => 'rejected',
            '3'     => 'active', // In Force
            '4'     => 'cancelled',
            '5'     => 'not_taken',
            '6'     => 'surrender',
            '7'     => 'matured',
            default => 'unknown',
        };
    }

    private function mapGlimsStatusReason(string $reasonCode): string
    {
        return match ((string) $reasonCode) {
            '3'     => 'New',
            '4'     => 'Alteration',
            '5'     => 'Renewal',
            '6'     => 'Extension',
            '8'     => 'Reinstatement',
            '9'     => 'Reversal of Alteration',
            '10'    => 'Reinstatement With Lapse',
            '11'    => 'Paid Up',
            '40'    => 'Suspended',
            '41'    => 'Cancelled',
            '42'    => 'Cancelled From Inception',
            '43'    => 'Reversal',
            '44'    => 'Automatic Cancellation',
            '50'    => 'Not Taken',
            '60'    => 'Surrender',
            '71'    => 'Matured',
            default => 'Unknown',
        };
    }

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

            // Skip policies that should never show on the portal
            $skipStatuses = ['0', '2', '5'];    // Quote, Rejected Proposal, Not Taken
            $skipReasons  = ['41', '42', '44']; // Cancelled, Cancelled From Inception, Auto Cancellation

            if (in_array($raw['POLICY_STATUS'] ?? '', $skipStatuses) ||
                in_array($raw['POLICY_STATUS_REASON'] ?? '', $skipReasons)) {
                continue;
            }

            // Pull motor risks to get the vehicle number (if motor policy)
            $motorRisks    = $this->glims->getMotorRisks($raw['POLICY_SEQUENCE']);
            $vehicleNumber = ! empty($motorRisks)
                ? ((array) $motorRisks[0])['objecth_02_plate_number'] ?? null
                : null;

            $policy = Policy::updateOrCreate(
                ['external_policy_id' => (string) $raw['POLICY_SEQUENCE']],
                [
                    'customer_id'         => $dbCustomer->id,
                    'source'              => 'glims',
                    'policy_number'       => $raw['POLICY_NUMBER'],
                    'insured_name'        => $dbCustomer->name,

                    // Raw IDs for internal reference
                    'business_class_id'   => $raw['POLICY_LOB_ID'] ?? null,
                    'product_id'          => $raw['POLICY_PRODUCT_ID'] ?? null,

                    // Resolved human-readable names for display
                    'business_class_name' => $raw['POLICY_LOB_NAME'] ?? 'Unknown Class',
                    'product_name'        => $raw['POLICY_PRODUCT_NAME'] ?? 'Unknown Product',

                    'start_date'          => $raw['POLICY_COMMENCEMENT_DATE'],
                    'end_date'            => $raw['POLICY_EXPIRY_DATE'],
                    'effective_date'      => $raw['POLICY_EFFECTIVE_DATE'],
                    'renewal_date'        => null,
                    // Store the human-readable status derived from POLICY_STATUS
                    'status'              => $this->mapGlimsStatus($raw['POLICY_STATUS'] ?? ''),
                    'raw_payload'         => array_merge($raw, [
                        'vehicle_number' => $vehicleNumber,
                        'motor_risks'    => $motorRisks,
                        'status_label'   => $this->mapGlimsStatus($raw['POLICY_STATUS'] ?? ''),
                        'status_reason'  => $this->mapGlimsStatusReason($raw['POLICY_STATUS_REASON'] ?? ''),
                    ]),
                    'last_synced_at'      => now(),
                ]
            );

            // Response uses the same resolved names
            $syncedMap[$raw['POLICY_NUMBER']] = [
                'policy_id'           => $policy->external_policy_id,
                'policy_number'       => $policy->policy_number,
                'product_id'          => $policy->product_id,
                'product_name'        => $policy->product_name,
                'business_class_id'   => $policy->business_class_id,
                'business_class_name' => $policy->business_class_name,
                'lob_name'            => $raw['POLICY_LOB_NAME'] ?? null,
                'branch_name'         => $raw['POLICY_BRANCH_NAME'] ?? null,
                'agent_name'          => $raw['POLICY_AGENT_NAME'] ?? null,
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

    private function deriveStatus(?string $expiryDate): string
    {
        if (! $expiryDate) {
            return 'unknown';
        }

        return \Carbon\Carbon::parse($expiryDate)->isFuture() ? 'active' : 'expired';
    }
}
