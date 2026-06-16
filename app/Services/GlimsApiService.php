<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GlimsApiService
 *
 * HTTP client for the GLIMS CS-Middleware API.
 * Replaces the previous GlimsService which required a direct Oracle DB connection.
 *
 * Endpoints:
 *   GET /api/customers/search/?search_type=phone_number&search_value=X
 *   GET /api/customers/search/?search_type=customer_code&search_value=X
 *   GET /api/policies/search/?search_type=customer_code&search_value=X
 *   GET /api/policies/search/?search_type=agent_code&search_value=X
 *   GET /api/policies/details/?policy_number=X
 *   GET /api/claims/search/?search_type=customer_code&search_value=X
 *
 * Config: config/services.php → env: GLIMS_API_URL, GLIMS_API_KEY, GLIMS_API_SECRET
 */
class GlimsApiService
{
    private string $baseUrl;
    private string $apiKey;
    private string $apiSecret;

    public function __construct()
    {
        $this->baseUrl   = rtrim(config('services.glims.url'), '/');
        $this->apiKey    = config('services.glims.key', '');
        $this->apiSecret = config('services.glims.secret', '');
    }

    // Customer search
    /**
     * Search for a customer by phone number.
     */
    public function searchCustomerByPhone(string $phone): array
    {
        return $this->customerSearch('phone_number', $phone);
    }

    /**
     * Search for a customer by customer code.
     * Returns the same shape as searchCustomerByPhone().
     */
    public function searchCustomerByCode(string $customerCode): array
    {
        return $this->customerSearch('customer_code', $customerCode);
    }

    /**
     * Verify a customer exists and return a normalised customer array.
     * Returns null if not found.
     *
     * Drop-in replacement for the old GlimsService::customerVerification().
     */
    public function customerVerification(string $customerCode): ?array
    {
        $results = $this->searchCustomerByCode($customerCode);

        if (empty($results)) {
            Log::info('GlimsApiService: customer not found', ['customer_code' => $customerCode]);
            return null;
        }

        return $this->normaliseCustomer($results[0]);
    }

    /**
     * Resolve GLIMS profiles by phone number.
     * Used by AuthController::resolveGlimsProfiles().
     * Returns the same profile shape that the auth flow expects.
     */
    public function resolveProfilesByPhone(string $phone): array
    {
        $results = $this->searchCustomerByPhone($phone);

        if (empty($results)) {
            return [];
        }

        return collect($results)->map(function ($row) {
            $customer = $this->normaliseCustomer($row);

            return [
                'code'         => $customer['CLIENT_CODE'],
                'name'         => $customer['FULL_NAME'],
                'phone'        => $customer['CLIENT_HOME_MOBILE'],
                'email'        => $customer['CLIENT_HOME_EMAIL'],
                'policy_count' => null, // avoid an extra API call during auth
                'source'       => 'glims',
                'is_match'     => true,
            ];
        })->values()->toArray();
    }

    /**
     * Resolve GLIMS profiles by customer code.
     * Used when we have the code but need the profile shape.
     */
    public function resolveProfilesByCustomerCode(string $customerCode): array
    {
        $results = $this->searchCustomerByCode($customerCode);

        if (empty($results)) {
            return [];
        }

        return collect($results)->map(function ($row) {
            $customer = $this->normaliseCustomer($row);

            return [
                'code'         => $customer['CLIENT_CODE'],
                'name'         => $customer['FULL_NAME'],
                'phone'        => $customer['CLIENT_HOME_MOBILE'],
                'email'        => $customer['CLIENT_HOME_EMAIL'],
                'policy_count' => null,
                'source'       => 'glims',
                'is_match'     => true,
            ];
        })->values()->toArray();
    }

    // Policy search
    /**
     * Get all policies for a customer code as grouped policy records.
     * Fleet policies (multiple plate_numbers under one policy_number) are
     * collapsed into one entry with a 'risks' array.
     * Drop-in replacement for GlimsService::getPoliciesByClientCode().
     */
    public function getPoliciesByClientCode(string $clientCode): array
    {
        $rows = $this->policySearch('customer_code', $clientCode);

        if (empty($rows)) {
            return [];
        }

        return $this->groupRowsIntoPolicies($rows);
    }

    /**
     * Get all policies for an agent code.
     */
    public function getPoliciesByAgentCode(string $agentCode): array
    {
        $rows = $this->policySearch('agent_code', $agentCode);

        if (empty($rows)) {
            return [];
        }

        return $this->groupRowsIntoPolicies($rows);
    }

    // ── Policy details ────────────────────────────────────────────────────────

    /**
     * Get rich vehicle/risk detail for a specific policy number.
     * This is the GLIMS equivalent of Genova's policySearch() rich endpoint.
     */
    public function getPolicyDetails(string $policyNumber): array
    {
        try {
            $response = $this->http()->get("{$this->baseUrl}/api/policies/details/", [
                'policy_number' => $policyNumber,
            ]);

            if ($response->failed()) {
                Log::warning('GlimsApiService: policy details failed', [
                    'policy_number' => $policyNumber,
                    'status'        => $response->status(),
                ]);
                return [];
            }

            return $response->json('results') ?? [];

        } catch (\Exception $e) {
            Log::error('GlimsApiService: getPolicyDetails error — ' . $e->getMessage(), [
                'policy_number' => $policyNumber,
            ]);
            return [];
        }
    }

    /**
     * Get policy details and map them to the normalised risk shape
     * that PolicyResource::extractGlimsRisks() expects.
     * A policy_number may have multiple detail rows (one per vehicle/risk),
     * so this returns an array of risks.
     */
    public function getRisksForPolicy(string $policyNumber): array
    {
        $details = $this->getPolicyDetails($policyNumber);
        if (empty($details)) {
            return [];
        }
        return collect($details)->map(fn($d) => $this->normaliseDetailToRisk($d))->values()->toArray();
    }

    // Claims search
    /**
     * Get claims for a customer code.
     * GET /api/claims/search/?search_type=customer_code&search_value=X
     */
    public function getClaimsByCustomerCode(string $customerCode): array
    {
        try {
            $response = $this->http()->get("{$this->baseUrl}/api/claims/search/", [
                'search_type'  => 'customer_code',
                'search_value' => $customerCode,
            ]);

            if ($response->failed()) {
                Log::warning('GlimsApiService: claims search failed', [
                    'customer_code' => $customerCode,
                    'status'        => $response->status(),
                ]);
                return [];
            }

            return $response->json('results') ?? [];

        } catch (\Exception $e) {
            Log::error('GlimsApiService: getClaimsByCustomerCode error — ' . $e->getMessage());
            return [];
        }
    }

    // Health check
    /**
     * Confirm the middleware API is reachable.
     * Drop-in replacement for GlimsService::isConnected().
     */
    public function isConnected(): bool
    {
        try {
            // A lightweight customer search with a dummy value —
            // we expect a 200 with count=0, not a connection error
            $response = $this->http()->get("{$this->baseUrl}/api/customers/search/", [
                'search_type'  => 'customer_code',
                'search_value' => '__ping__',
            ]);

            return $response->status() > 0;

        } catch (\Exception $e) {
            Log::debug('GlimsApiService: health check failed — ' . $e->getMessage());
            return false;
        }
    }

    // Private: HTTP layer

    private function http()
    {
        return Http::withHeaders([
            'x-api-key'    => $this->apiKey,
            'x-api-secret' => $this->apiSecret,
            'Accept'       => 'application/json',
        ])->timeout(15);
    }

    private function customerSearch(string $searchType, string $searchValue): array
    {
        try {
            $response = $this->http()->get("{$this->baseUrl}/api/customers/search/", [
                'search_type'  => $searchType,
                'search_value' => $searchValue,
            ]);

            if ($response->failed()) {
                Log::warning('GlimsApiService: customer search failed', [
                    'search_type'  => $searchType,
                    'search_value' => $searchValue,
                    'status'       => $response->status(),
                ]);
                return [];
            }

            return $response->json('results') ?? [];

        } catch (\Exception $e) {
            Log::error('GlimsApiService: customerSearch error — ' . $e->getMessage(), [
                'search_type'  => $searchType,
                'search_value' => $searchValue,
            ]);
            return [];
        }
    }

    private function policySearch(string $searchType, string $searchValue): array
    {
        try {
            $response = $this->http()->get("{$this->baseUrl}/api/policies/search/", [
                'search_type'  => $searchType,
                'search_value' => $searchValue,
            ]);

            if ($response->failed()) {
                Log::warning('GlimsApiService: policy search failed', [
                    'search_type'  => $searchType,
                    'search_value' => $searchValue,
                    'status'       => $response->status(),
                ]);
                return [];
            }

            return $response->json('results') ?? [];

        } catch (\Exception $e) {
            Log::error('GlimsApiService: policySearch error — ' . $e->getMessage(), [
                'search_type'  => $searchType,
                'search_value' => $searchValue,
            ]);
            return [];
        }
    }

    // Private: Normalisation
    /**
     * Normalise a customer search result row into a consistent internal shape.
     */
    private function normaliseCustomer(array $row): array
    {
        $firstName  = $row['first_name'] ?? '';
        $otherNames = $row['other_names'] ?? '';
        $familyName = $row['family_name'] ?? '';

        $fullName = trim(implode(' ', array_filter([
            $firstName,
            $otherNames,
            $familyName,
        ])));

        return [
            // Keys match old GlimsService shape so callers don't need updating
            'CLIENT_CODE'        => (string) ($row['customer_code'] ?? ''),
            'CLIENT_FIRST_NAME'  => $firstName,
            'CLIENT_MIDDLE_NAME' => $otherNames,
            'CLIENT_FAMILY_NAME' => $familyName,
            'FULL_NAME'          => $fullName ?: 'Unknown',
            'CLIENT_HOME_MOBILE' => $row['mobile_number'] ?? null,
            'CLIENT_HOME_TEL'    => null,
            'CLIENT_HOME_EMAIL'  => $row['email'] ?? null,
            'CLIENT_TYPE'        => $row['client_type'] ?? null,
            // Extra fields now available from the dedicated customer endpoint
            'CLIENT_ID_NUMBER'   => $row['id_number'] ?? null,
            'CLIENT_GENDER'      => $row['gender'] ?? null,
            'CLIENT_DOB'         => $row['date_of_birth'] ?? null,
            'CLIENT_CREATED_AT'  => $row['created_at'] ?? null,
            // Keep the raw row for raw_payload storage
            '_raw'               => $row,
        ];
    }

    /**
     * Group flat policy search rows into one record per policy_number.
     * Fleet logic:
     *   Multiple rows sharing a policy_number → one policy, multiple risks.
     *   Each row's plate_number becomes a risk entry.
     *   Financials (premium, sum_insured) are summed across the group.
     */
    private function groupRowsIntoPolicies(array $rows): array
    {
        return collect($rows)
            ->groupBy('policy_number')
            ->map(function ($group) {
                $first = (array) $group->first();

                // Collect plate_number values as placeholder risks.
                // Full vehicle detail is fetched separately via getPolicyDetails()
                // during the sync job (same pattern as Genova's rich sync).
                $risks = $group
                    ->filter(fn($row) => ! empty(((array) $row)['plate_number']))
                    ->map(fn($row) => $this->normalisePlaceholderRisk((array) $row))
                    ->values()
                    ->toArray();

                return [
                    'POLICY_NUMBER'        => $first['policy_number'] ?? null,
                    'POLICY_ID'            => $first['policy_id'] ?? null,
                    'POLICY_CREATED_AT'    => $first['policy_created_at'] ?? null,
                    'POLICY_START_DATE'    => $first['start_date'] ?? null,
                    'POLICY_EXPIRY_DATE'   => $first['expiry_date'] ?? null,
                    'POLICY_ISSUE_DATE'    => $first['issue_date'] ?? null,
                    'POLICY_LOB_NAME'      => $first['lob'] ?? null,
                    'POLICY_PRODUCT_NAME'  => $first['product'] ?? null,
                    'POLICY_PRODUCT_CODE'  => $first['product_code'] ?? null,
                    'POLICY_BRANCH_NAME'   => $first['branch_name'] ?? null,
                    'POLICY_AGENT_CODE'    => $first['agent_code'] ?? null,
                    'POLICY_AGENT_NAME'    => $first['intermediary_name'] ?? null,
                    'POLICY_CURRENCY'      => $first['currency'] ?? null,
                    'POLICY_TOTAL_PREMIUM' => $group->sum('premium'),
                    'POLICY_TOTAL_SI'      => $group->sum('sum_insured'),
                    'CUSTOMER_CODE'        => (string) ($first['customer_code'] ?? ''),
                    'CUSTOMER_FIRST_NAME'  => $first['first_name'] ?? null,
                    'CUSTOMER_OTHER_NAMES' => $first['other_names'] ?? null,
                    'CUSTOMER_FAMILY_NAME' => $first['family_name'] ?? null,
                    'risks'                => $risks,
                    'is_fleet'             => count($risks) > 1,
                ];
            })
            ->values()
            ->toArray();
    }

    /**
     * Placeholder risk from a policy search row.
     * Has plate_number + financials only — no vehicle detail yet.
     * Full detail is filled in by normaliseDetailToRisk() during the sync job.
     */
    private function normalisePlaceholderRisk(array $row): array
    {
        return [
            'risk_ref_no'            => $row['plate_number'],
            'vehicle_make'           => null,
            'vehicle_model'          => null,
            'vehicle_yr_manufacture' => null,
            'vehicle_chassis_no'     => null,
            'vehicle_colour'         => null,
            'vehicle_body_type'      => null,
            'sum_insured'            => $row['sum_insured'] ?? null,
            'total_premium'          => $row['premium'] ?? null,
            'covers'                 => [],
            '_raw'                   => $row,
        ];
    }

    /**
     * Normalise a policy details row into the full risk shape.
     * This is what gets stored in raw_payload['risks'] after the rich sync.
     */
    private function normaliseDetailToRisk(array $detail): array
    {
        return [
            'risk_ref_no'            => $detail['vehicle_no'] ?? null,
            'vehicle_make'           => $detail['vehicle_make'] ?? null,
            'vehicle_model'          => $detail['vehicle_model'] ?? null,
            'vehicle_yr_manufacture' => $detail['year_of_manufacture'] ?? null,
            'vehicle_chassis_no'     => $detail['chassis'] ?? null,
            'vehicle_colour'         => null, // not in details response
            'vehicle_body_type'      => $detail['body_type'] ?? null,
            'seats'                  => $detail['seats'] ?? null,
            'cubic_capacity'         => $detail['cubic_capacity'] ?? null,
            'usage'                  => $detail['usage'] ?? null,
            'sum_insured'            => $detail['sum_insured'] ?? null,
            'total_premium'          => $detail['total_premium'] ?? null,
            'covers'                 => [], // not in details response
            '_raw'                   => $detail,
        ];
    }
}
