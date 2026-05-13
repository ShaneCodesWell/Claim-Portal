<?php
namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GlimsService
{
    private $connection;

    public function __construct()
    {
        $this->connection = 'oracle';
    }

    private function db()
    {
        return DB::connection($this->connection);
    }

    /**
     * Verify a customer exists in GLIMS by phone, policy number, or client code.
     * Mirrors: GenovaApiService::customerVerification()
     */
    public function customerVerification(string $identifier, string $type = 'phone'): ?array
    {
        try {
            $customer = $this->db()
                ->table('GN2_CLIENT')
                ->where('CLIENT_CODE', $identifier)
                ->select([
                    'CLIENT_CODE',
                    'CLIENT_FIRST_NAME',
                    'CLIENT_MIDDLE_NAME',
                    'CLIENT_FAMILY_NAME',
                    'CLIENT_HOME_MOBILE',
                    'CLIENT_HOME_TEL',
                    'CLIENT_HOME_EMAIL',
                    'CLIENT_TYPE',
                ])
                ->first();

            if (! $customer) {
                Log::info('GLIMS: Customer not found', ['identifier' => $identifier]);
                return null;
            }

            return [
                'CLIENT_CODE'        => $customer->client_code,
                'CLIENT_FIRST_NAME'  => $customer->client_first_name,
                'CLIENT_MIDDLE_NAME' => $customer->client_middle_name,
                'CLIENT_FAMILY_NAME' => $customer->client_family_name,
                'CLIENT_HOME_MOBILE' => $customer->client_home_mobile,
                'CLIENT_HOME_TEL'    => $customer->client_home_tel,
                'CLIENT_HOME_EMAIL'  => $customer->client_home_email,
                'CLIENT_TYPE'        => $customer->client_type,
            ];

        } catch (\Exception $e) {
            Log::error('GLIMS customerVerification error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all active policies for a customer, with human-readable names
     * resolved via LEFT JOINs to the GLIMS reference/lookup tables.
     *
     * Resolved fields:
     *   POLICY_PRODUCT      -> UW2_PRODUCT.PRODUCT_DESC      (via PRODUCT_CODE)
     *   POLICY_LOB          -> UW2_SUB_CLASS.SUB_CLASS_DESC   (via SUB_CLASS_CODE)
     *   POLICY_MAIN_CLASS   -> UW2_MAIN_CLASS.MAIN_CLASS_DESC (via SUB_CLASS_MAIN)
     *   POLICY_BRANCH       -> GN2_BRANCH.BRANCH_DESC         (via BRANCH_CODE)
     *   POLICY_AGENT        -> GN2_AGENT full name            (via AGENT_CODE)
     *   POLICY_NUMBER       -> constructed as P-{PRODUCT}-{BRANCH}-{UWY}-{PROPOSAL}
     */
    public function getPoliciesByClientCode(string $clientCode): array
    {
        try {
            $policies = $this->db()
                ->table('UW1_POLICY as p')
            // Product lookup
                ->leftJoin('UW2_PRODUCT as prod', 'prod.PRODUCT_CODE', '=', 'p.POLICY_PRODUCT')
            // Line-of-Business (sub-class) lookup
                ->leftJoin('UW2_SUB_CLASS as sc', 'sc.SUB_CLASS_CODE', '=', 'p.POLICY_LOB')
            // Main class lookup — linked through sub-class via SUB_CLASS_MAIN
                ->leftJoin('UW2_MAIN_CLASS as mc', 'mc.MAIN_CLASS_CODE', '=', 'sc.SUB_CLASS_MAIN')
            // Branch lookup
                ->leftJoin('GN2_BRANCH as br', 'br.BRANCH_CODE', '=', 'p.POLICY_BRANCH')
            // Agent lookup
                ->leftJoin('GN2_AGENT as ag', 'ag.AGENT_CODE', '=', 'p.POLICY_AGENT')
                ->where('p.POLICY_OWNER', $clientCode)
                ->select([
                    // Core policy fields
                    'p.POLICY_SEQUENCE',
                    'p.POLICY_OWNER',
                    'p.POLICY_INSURED',
                    'p.POLICY_STATUS',
                    'p.POLICY_STATUS_REASON',
                    'p.POLICY_CURRENCY',
                    'p.POLICY_TOTAL_PREMIUM',
                    'p.POLICY_TOTAL_SI',
                    'p.POLICY_COMMENCEMENT_DATE',
                    'p.POLICY_EXPIRY_DATE',
                    'p.POLICY_EFFECTIVE_DATE',
                    'p.POLICY_ENDORSEMENT_DATE',

                    // Raw ID fields (kept for reference/filtering)
                    'p.POLICY_PRODUCT      as POLICY_PRODUCT_ID',
                    'p.POLICY_LOB          as POLICY_LOB_ID',
                    'p.POLICY_MAIN_CLASS   as POLICY_MAIN_CLASS_ID',
                    'p.POLICY_BRANCH       as POLICY_BRANCH_ID',
                    'p.POLICY_AGENT        as POLICY_AGENT_ID',

                    // Resolved human-readable names
                    'prod.PRODUCT_DESC     as POLICY_PRODUCT_NAME',
                    'sc.SUB_CLASS_DESC     as POLICY_LOB_NAME',
                    'mc.MAIN_CLASS_DESC    as POLICY_MAIN_CLASS_NAME',
                    'br.BRANCH_DESC        as POLICY_BRANCH_NAME',
                ])
            // Agent full name — concatenated in PHP after fetch (Oracle CONCAT is 2-arg only)
                ->addSelect(
                    DB::raw("ag.AGENT_FIRST_NAME || ' ' || ag.AGENT_FAMILY_NAME as POLICY_AGENT_NAME"),
                    // Formatted policy number: P-{PRODUCT}-{BRANCH}-{UWY}-{PROPOSAL}
                    DB::raw("'P-' || LPAD(p.POLICY_PRODUCT, 3, '0') || '-' || p.POLICY_BRANCH || '-' || p.POLICY_UWY || '-' || LPAD(p.POLICY_PROPOSAL, 6, '0') as POLICY_FORMATTED_NUMBER")
                )
                ->orderBy('p.POLICY_COMMENCEMENT_DATE', 'desc')
                ->get();

            // Per policy number: prefer the latest active, fallback to latest overall
            $policies = $policies
                ->groupBy('policy_number')
                ->map(function ($group) {
                    // Try to find an active policy (status 3 = In Force)
                    $active = $group->first(fn($p) => $p->policy_status == 3);
                    return $active ?? $group->first(); // fallback to most recent (already ordered by date desc)
                })
                ->values();

            // Remap lowercase Oracle keys to uppercase + convert Julian dates
            return $policies->map(function ($row) {
                return [
                    // Core
                    'POLICY_SEQUENCE'          => $row->policy_sequence,
                    'POLICY_NUMBER'            => $row->policy_formatted_number ?? $row->policy_sequence,
                    'POLICY_OWNER'             => $row->policy_owner,
                    'POLICY_INSURED'           => $row->policy_insured,
                    'POLICY_STATUS'            => $row->policy_status,
                    'POLICY_STATUS_REASON'     => $row->policy_status_reason,
                    'POLICY_CURRENCY'          => $row->policy_currency,
                    'POLICY_TOTAL_PREMIUM'     => $row->policy_total_premium,
                    'POLICY_TOTAL_SI'          => $row->policy_total_si,

                    // Dates (Julian → Y-m-d)
                    'POLICY_COMMENCEMENT_DATE' => $this->julianToDate($row->policy_commencement_date),
                    'POLICY_EXPIRY_DATE'       => $this->julianToDate($row->policy_expiry_date),
                    'POLICY_EFFECTIVE_DATE'    => $this->julianToDate($row->policy_effective_date),
                    'POLICY_ENDORSEMENT_DATE'  => $this->julianToDate($row->policy_endorsement_date),

                    // Raw IDs (for internal use / debugging)
                    'POLICY_PRODUCT_ID'        => $row->policy_product_id,
                    'POLICY_LOB_ID'            => $row->policy_lob_id,
                    'POLICY_MAIN_CLASS_ID'     => $row->policy_main_class_id,
                    'POLICY_BRANCH_ID'         => $row->policy_branch_id,
                    'POLICY_AGENT_ID'          => $row->policy_agent_id,

                    // Human-readable resolved names
                    'POLICY_PRODUCT_NAME'      => $row->policy_product_name ?? 'Unknown Product',
                    'POLICY_LOB_NAME'          => $row->policy_lob_name ?? 'Unknown LOB',
                    'POLICY_MAIN_CLASS_NAME'   => $row->policy_main_class_name ?? 'Unknown Class',
                    'POLICY_BRANCH_NAME'       => $row->policy_branch_name ?? 'Unknown Branch',
                    'POLICY_AGENT_NAME'        => $row->policy_agent_name ?? 'Unknown Agent',
                ];
            })->toArray();

        } catch (\Exception $e) {
            Log::error('GLIMS getPoliciesByClientCode error: ' . $e->getMessage(), [
                'client_code' => $clientCode,
            ]);
            return [];
        }
    }

    /**
     * Convert Oracle Julian day number to a Y-m-d date string.
     * Oracle Julian dates count days since January 1, 4713 BC.
     */
    private function julianToDate($julianDay): ?string
    {
        if (! $julianDay) {
            return null;
        }

        try {
            // PHP's Julian to date conversion
            $julian               = (int) $julianDay;
            [$month, $day, $year] = explode('/', jdtogregorian($julian));
            return \Carbon\Carbon::createFromDate($year, $month, $day)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get motor (car) risk details for a policy.
     */
    public function getMotorRisks(string $policySequence): array
    {
        try {
            $risks = $this->db()->table('UW1_OBJECTH')
                ->where('OBJECTH_SEQUENCE', $policySequence)
                ->select([
                    'OBJECTH_SEQUENCE',
                    'OBJECTH_02_PLATE_NUMBER', // vehicle registration e.g. "AW 3888-14"
                    'OBJECTH_02_MAKE',
                    'OBJECTH_02_MODEL',
                    'OBJECTH_02_YEAR',
                    'OBJECTH_02_CHASSIS',
                    'OBJECTH_02_COLOUR',
                    'OBJECTH_SI',
                    'OBJECTH_PREMIUM',
                ])
                ->get();

            return $risks->toArray();

        } catch (\Exception $e) {
            Log::error('GLIMS getMotorRisks error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get non-motor risk details for a policy (fire, bonds, engineering etc.)
     */
    public function getNonMotorRisks(string $policySequence): array
    {
        try {
            $risks = $this->db()->table('UW1_OBJECTD1')
                ->where('POLICY_SEQUENCE', $policySequence)
                ->select([
                    'OBJECTD1_SEQUENCE',
                    'OBJECTD1_ID',
                    'POLICY_SEQUENCE',
                    'SUM_INSURED',
                    'RATE',
                    'RISK_DESCRIPTION',
                    'PREMIUM',
                ])
                ->get();

            return $risks->toArray();

        } catch (\Exception $e) {
            Log::error('GLIMS getNonMotorRisks error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Get claim transactions for a policy.
     */
    public function getClaimsByPolicySequence(string $policySequence): array
    {
        try {
            $claims = $this->db()
                ->table('CL1_CLAIM_TRANSACTIONS as ct')
                ->join('CL1_CLAIMH as ch', 'ch.CLAIMH_SEQUENCE', '=', 'ct.CLAIMTRANS_SEQUENCE')
                ->where('ct.POLICY_SEQUENCE', $policySequence)
                ->select([
                    'ct.CLAIMTRANS_SEQUENCE',
                    'ct.POLICY_SEQUENCE',
                    'ch.RISK_ID',
                    'ch.LOSS_DATE',
                    'ch.REPORTED_DATE',
                    'ch.ENTRY_DATE',
                    'ch.CLAIMANTS_NARRATION',
                    'ct.ENTRY_TYPE',
                    'ct.AMOUNT',
                ])
                ->orderBy('ch.REPORTED_DATE', 'desc')
                ->get();

            return $claims->toArray();

        } catch (\Exception $e) {
            Log::error('GLIMS getClaimsByPolicySequence error: ' . $e->getMessage(), [
                'policy_sequence' => $policySequence,
            ]);
            return [];
        }
    }

    /**
     * Health check — confirm Oracle connection is alive.
     */
    public function isConnected(): bool
    {
        try {
            $this->db()->select('SELECT 1 FROM DUAL');
            return true;
        } catch (\Exception $e) {
            Log::error('GLIMS connection check failed: ' . $e->getMessage());
            return false;
        }
    }

}
