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
    // public function customerVerification(string $identifier, string $type = 'phone'): ?array
    // {
    //     try {
    //         $query = $this->db()->table('GN2_CLIENT as c');

    //         switch ($type) {
    //             case 'phone':
    //             case 'mobile_no':
    //                 $query->where('c.MOBILE_NO', $identifier)
    //                     ->orWhere('c.PHONE_NO', $identifier);
    //                 break;

    //             case 'policy_number':
    //                 $query->join('UW1_POLICY as p', 'p.CLIENT_CODE', '=', 'c.CLIENT_CODE')
    //                     ->where('p.POLICY_NO', $identifier);
    //                 break;

    //             case 'client_code':
    //                 $query->where('c.CLIENT_CODE', (int) $identifier);
    //                 break;

    //             default:
    //                 $query->where('c.MOBILE_NO', $identifier);
    //         }

    //         $customer = $query->select([
    //             'c.CLIENT_CODE',
    //             'c.FIRST_NAME',
    //             'c.MIDDLE_NAME',
    //             'c.LAST_NAME',
    //             'c.DATE_OF_BIRTH',
    //             'c.MOBILE_NO',
    //             'c.PHONE_NO',
    //             'c.EMAIL',
    //             'c.CLIENT_TYPE',
    //         ])->first();

    //         if (! $customer) {
    //             Log::info('GLIMS: Customer not found', ['identifier' => $identifier, 'type' => $type]);
    //             return null;
    //         }

    //         return (array) $customer;

    //     } catch (\Exception $e) {
    //         Log::error('GLIMS customerVerification error: ' . $e->getMessage());
    //         return null;
    //     }
    // }
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

            // Remap to uppercase keys so GlimsSyncService can access them consistently
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
     * Get all active policies for a customer.
     * Mirrors: GenovaApiService::getPolicies()
     */
    public function getPoliciesByClientCode(string $clientCode): array
    {
        try {
            $policies = $this->db()->table('UW1_POLICY')
                ->where('POLICY_OWNER', $clientCode)
                ->select([
                    'POLICY_SEQUENCE',
                    'POLICY_NUMBER',
                    'POLICY_OWNER',
                    'POLICY_INSURED',
                    'POLICY_BRANCH',
                    'POLICY_AGENT',
                    'POLICY_STATUS',        // ← main status (3 = In Force, 4 = Cancelled etc.)
                    'POLICY_STATUS_REASON', // ← sub-status type (5 = Renewal, 41 = Cancelled etc.)
                    'POLICY_MAIN_CLASS',
                    'POLICY_LOB',
                    'POLICY_PRODUCT',
                    'POLICY_COMMENCEMENT_DATE',
                    'POLICY_EXPIRY_DATE',
                    'POLICY_EFFECTIVE_DATE',
                    'POLICY_ENDORSEMENT_DATE',
                    'POLICY_TOTAL_PREMIUM',
                    'POLICY_TOTAL_SI',
                    'POLICY_CURRENCY',
                ])
                ->orderBy('POLICY_COMMENCEMENT_DATE', 'desc')
                ->get();

            // Remap lowercase Oracle keys to uppercase + convert Julian dates
            return $policies->map(function ($row) {
                return [
                    'POLICY_SEQUENCE'          => $row->policy_sequence,
                    'POLICY_NUMBER'            => $row->policy_number,
                    'POLICY_OWNER'             => $row->policy_owner,
                    'POLICY_INSURED'           => $row->policy_insured,
                    'POLICY_BRANCH'            => $row->policy_branch,
                    'POLICY_AGENT'             => $row->policy_agent,
                    'POLICY_STATUS'            => $row->policy_status,
                    'POLICY_STATUS_REASON'     => $row->policy_status_reason,
                    'POLICY_MAIN_CLASS'        => $row->policy_main_class,
                    'POLICY_LOB'               => $row->policy_lob,
                    'POLICY_PRODUCT'           => $row->policy_product,
                    'POLICY_COMMENCEMENT_DATE' => $this->julianToDate($row->policy_commencement_date),
                    'POLICY_EXPIRY_DATE'       => $this->julianToDate($row->policy_expiry_date),
                    'POLICY_EFFECTIVE_DATE'    => $this->julianToDate($row->policy_effective_date),
                    'POLICY_ENDORSEMENT_DATE'  => $this->julianToDate($row->policy_endorsement_date),
                    'POLICY_TOTAL_PREMIUM'     => $row->policy_total_premium,
                    'POLICY_TOTAL_SI'          => $row->policy_total_si,
                    'POLICY_CURRENCY'          => $row->policy_currency,
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
                ->where('POLICY_SEQUENCE', $policySequence)
                ->select([
                    'OBJECT_SEQUENCE',
                    'POLICY_SEQUENCE',
                    'RISK_ID',
                    'YOM',
                    'MAKE',
                    'MODEL',
                    'CAR_NO',
                    'SUM_INSURED',
                    'PREMIUM',
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
     * Mirrors the claims data you'd get from Genova.
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
                    'ct.ENTRY_TYPE', // expense or settled
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
