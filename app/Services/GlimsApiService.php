<?php
namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * GlimsApiService
 *
 * HTTP client for the GLIMS CS-Middleware API.
 * Replaces the previous GlimsService which required a direct Oracle DB connection.
 *
 * Base URL + credentials are read from config/services.php → env:
 *   GLIMS_API_URL    = https://glive.vanguardassurance.com/cs-middleware
 *   GLIMS_API_KEY    = ...
 *   GLIMS_API_SECRET = ...
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

    // ── Core search ───────────────────────────────────────────────────────────

    /**
     * Search policies by customer code.
     * Returns all policy rows for that customer as a flat array.
     * Each row is one policy (or one vehicle under a fleet policy).
     *
     * GET /api/policies/search?search_type=customer_code&search_value={code}
     */
    public function searchByCustomerCode(string $customerCode): array
    {
        return $this->search('customer_code', $customerCode);
    }

    /**
     * Search policies by agent code.
     *
     * GET /api/policies/search?search_type=agent_code&search_value={code}
     */
    public function searchByAgentCode(string $agentCode): array
    {
        return $this->search('agent_code', $agentCode);
    }

    /**
     * Search policies by phone number.
     * NOTE: Not yet supported by the middleware — request pending with IT.
     * Kept here so the controller can call it once the API is updated.
     *
     * GET /api/policies/search?search_type=phone_number&search_value={phone}
     */
    public function searchByPhone(string $phone): array
    {
        return $this->search('phone_number', $phone);
    }

    // ── Customer helpers ──────────────────────────────────────────────────────

    /**
     * Verify a customer exists in GLIMS by customer code.
     * Returns a normalised customer array (name + contact fields),
     * or null if not found.
     *
     * Mirrors GlimsService::customerVerification() for callers that
     * still use that method signature.
     */
    public function customerVerification(string $customerCode): ?array
    {
        $rows = $this->searchByCustomerCode($customerCode);

        if (empty($rows)) {
            Log::info('GlimsApiService: customer not found', ['customer_code' => $customerCode]);
            return null;
        }

        // Customer identity is embedded in every row — use the first one
        $first = $rows[0];

        return $this->extractCustomerFromRow($first);
    }

    /**
     * Get all policies for a customer code, grouped by policy number.
     * Fleet policies (multiple plate numbers under one policy number)
     * are collapsed into a single entry whose 'risks' key holds all vehicles.
     *
     * Mirrors GlimsService::getPoliciesByClientCode() for callers that
     * still use that method signature.
     */
    public function getPoliciesByClientCode(string $clientCode): array
    {
        $rows = $this->searchByCustomerCode($clientCode);

        if (empty($rows)) {
            return [];
        }

        return $this->groupRowsIntoPolicies($rows);
    }

    // ── Profile resolution (for AuthController) ───────────────────────────────

    /**
     * Resolve GLIMS profiles for a customer code.
     * Returns the same shape that AuthController::resolveGlimsProfiles() expects.
     */
    public function resolveProfilesByCustomerCode(string $customerCode): array
    {
        $rows = $this->searchByCustomerCode($customerCode);

        if (empty($rows)) {
            return [];
        }

        $first    = $rows[0];
        $customer = $this->extractCustomerFromRow($first);

        return [[
            'code'         => $customer['CLIENT_CODE'],
            'name'         => $customer['FULL_NAME'],
            'phone'        => $customer['CLIENT_HOME_MOBILE'] ?? null,
            'email'        => $customer['CLIENT_HOME_EMAIL'] ?? null,
            'policy_count' => count($this->groupRowsIntoPolicies($rows)),
            'source'       => 'glims',
            'is_match'     => true,
        ]];
    }

    /**
     * Resolve GLIMS profiles by phone number.
     * NOTE: Requires phone_number search_type support from IT.
     * Will return empty array until that is available.
     */
    public function resolveProfilesByPhone(string $phone): array
    {
        $rows = $this->searchByPhone($phone);

        if (empty($rows)) {
            return [];
        }

        // Group by customer_code so we return one profile per customer
        // (an agent search can return many customers — a phone search should return one,
        //  but we guard against duplicates just in case)
        $byCustomer = collect($rows)->groupBy('customer_code');

        $profiles = [];

        foreach ($byCustomer as $customerCode => $customerRows) {
            $first    = $customerRows->first();
            $customer = $this->extractCustomerFromRow((array) $first);

            $profiles[] = [
                'code'         => $customer['CLIENT_CODE'],
                'name'         => $customer['FULL_NAME'],
                'phone'        => $customer['CLIENT_HOME_MOBILE'] ?? $phone,
                'email'        => $customer['CLIENT_HOME_EMAIL'] ?? null,
                'policy_count' => count($this->groupRowsIntoPolicies($customerRows->toArray())),
                'source'       => 'glims',
                'is_match'     => true,
            ];
        }

        return $profiles;
    }

    // ── Health check ──────────────────────────────────────────────────────────

    /**
     * Confirm the middleware API is reachable.
     * Used as a drop-in replacement for GlimsService::isConnected().
     */
    public function isConnected(): bool
    {
        try {
            $response = $this->makeRequest('customer_code', 'PING_CHECK');
            // A 200 or 404 both mean the API is up — only connection errors mean it's down
            return $response->status() !== 0;
        } catch (\Exception $e) {
            Log::debug('GlimsApiService: health check failed — ' . $e->getMessage());
            return false;
        }
    }

    // ── Private: HTTP layer ───────────────────────────────────────────────────

    private function search(string $searchType, string $searchValue): array
    {
        try {
            $response = $this->makeRequest($searchType, $searchValue);

            if ($response->failed()) {
                Log::warning('GlimsApiService: search failed', [
                    'search_type'  => $searchType,
                    'search_value' => $searchValue,
                    'status'       => $response->status(),
                ]);
                return [];
            }

            return $response->json('results') ?? [];

        } catch (\Exception $e) {
            Log::error('GlimsApiService: HTTP error — ' . $e->getMessage(), [
                'search_type'  => $searchType,
                'search_value' => $searchValue,
            ]);
            return [];
        }
    }

    private function makeRequest(string $searchType, string $searchValue): Response
    {
        return Http::withHeaders([
            'x-api-key'    => $this->apiKey,
            'x-api-secret' => $this->apiSecret,
            'Accept'       => 'application/json',
        ])
            ->timeout(15)
            ->get("{$this->baseUrl}/api/policies/search", [
                'search_type'  => $searchType,
                'search_value' => $searchValue,
            ]);
    }

    // ── Private: Payload normalisation ───────────────────────────────────────

    /**
     * Extract a normalised customer record from a single policy row.
     * The middleware embeds customer identity in every policy row.
     */
    private function extractCustomerFromRow(array $row): array
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
            'CLIENT_CODE'        => (string) ($row['customer_code'] ?? ''),
            'CLIENT_FIRST_NAME'  => $firstName,
            'CLIENT_MIDDLE_NAME' => $otherNames,
            'CLIENT_FAMILY_NAME' => $familyName,
            'FULL_NAME'          => $fullName ?: 'Unknown',
            'CLIENT_HOME_MOBILE' => $row['phone'] ?? null, // pending phone field in API
            'CLIENT_HOME_TEL'    => null,
            'CLIENT_HOME_EMAIL'  => $row['email'] ?? null, // pending email field in API
            'CLIENT_TYPE'        => null,
        ];
    }

    /**
     * Group flat policy rows into one record per policy_number.
     *
     * Fleet logic:
     *   - Multiple rows sharing a policy_number → one policy, multiple risks
     *   - Each row's plate_number becomes a risk entry
     *   - Rows with null plate_number are non-motor policies (fire, bonds, etc.)
     *
     * Returns an array of normalised policy arrays, each with a 'risks' key.
     */
    private function groupRowsIntoPolicies(array $rows): array
    {
        $grouped = collect($rows)->groupBy('policy_number');

        return $grouped->map(function ($group) {

            // Use the first row for all the policy-level fields
            $first = (array) $group->first();

            // Collect risks (vehicles) from all rows in the group
            $risks = $group
                ->filter(fn($row) => ! empty(((array) $row)['plate_number']))
                ->map(fn($row) => $this->normaliseRisk((array) $row))
                ->values()
                ->toArray();

            return [
                // Policy identity
                'POLICY_NUMBER'        => $first['policy_number'] ?? null,
                'POLICY_ID'            => $first['policy_id'] ?? null,
                'POLICY_CREATED_AT'    => $first['policy_created_at'] ?? null,

                // Dates (already ISO strings from the API — no Julian conversion needed)
                'POLICY_START_DATE'    => $first['start_date'] ?? null,
                'POLICY_EXPIRY_DATE'   => $first['expiry_date'] ?? null,
                'POLICY_ISSUE_DATE'    => $first['issue_date'] ?? null,

                // Classification
                'POLICY_LOB_NAME'      => $first['lob'] ?? null,
                'POLICY_PRODUCT_NAME'  => $first['product'] ?? null,
                'POLICY_PRODUCT_CODE'  => $first['product_code'] ?? null,
                'POLICY_BRANCH_NAME'   => $first['branch_name'] ?? null,
                'POLICY_AGENT_CODE'    => $first['agent_code'] ?? null,
                'POLICY_AGENT_NAME'    => $first['intermediary_name'] ?? null,

                // Financials — summed across all risks for fleet
                'POLICY_CURRENCY'      => $first['currency'] ?? null,
                'POLICY_TOTAL_PREMIUM' => $group->sum('premium'),
                'POLICY_TOTAL_SI'      => $group->sum('sum_insured'),

                // Customer identity (same on every row)
                'CUSTOMER_CODE'        => (string) ($first['customer_code'] ?? ''),
                'CUSTOMER_FIRST_NAME'  => $first['first_name'] ?? null,
                'CUSTOMER_OTHER_NAMES' => $first['other_names'] ?? null,
                'CUSTOMER_FAMILY_NAME' => $first['family_name'] ?? null,

                // Risks / vehicles
                'risks'                => $risks,

                // Fleet flag — useful for PolicyResource
                'is_fleet'             => count($risks) > 1,
            ];

        })->values()->toArray();
    }

    /**
     * Normalise a single row into a risk entry.
     * Mirrors the shape that PolicyResource::extractRisks() produces for Genova risks,
     * so the frontend gets consistent data regardless of source.
     */
    private function normaliseRisk(array $row): array
    {
        return [
            'risk_ref_no'            => $row['plate_number'] ?? null,
            'vehicle_make'           => null, // not in middleware response — request from IT if needed
            'vehicle_model'          => null,
            'vehicle_yr_manufacture' => null,
            'vehicle_chassis_no'     => null,
            'vehicle_colour'         => null,
            'vehicle_body_type'      => null,
            'sum_insured'            => $row['sum_insured'] ?? null,
            'total_premium'          => $row['premium'] ?? null,
            'covers'                 => [], // not in middleware response
                                            // Keep raw row for forward compatibility — easy to add new fields later
            '_raw'                   => $row,
        ];
    }
}
