<?php

namespace App\Services;

use App\Models\Agent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GlimsPolicyFallbackService
{
    private const POLICY_NUMBER_PATTERN = '/^P-\d{4}-\d{3}-\d{4}-\d{4,7}$/';

    public function __construct(private GlimsApiService $glims) {}

    public function normalizePolicyNumber(string $input): string
    {
        $value = strtoupper(trim($input));
        return preg_replace('/\s+/', '', $value);
    }

    public function normalizeVehicleNumber(string $input): string
    {
        $value = strtoupper(trim($input));
        return preg_replace('/\s+/', ' ', $value); // collapse internal multi-space, keep single spaces — plates use them
    }

    public function isValidPolicyNumber(string $normalized): bool
    {
        return (bool) preg_match(self::POLICY_NUMBER_PATTERN, $normalized);
    }

    /**
     * GLIMS-wide search by policy number, used when the policy isn't found
     * in the agent's own portfolio. Read-only — never persisted locally.
     */
    public function search(string $rawPolicyNumber): ?array
    {
        $policyNumber = $this->normalizePolicyNumber($rawPolicyNumber);

        if (! $this->isValidPolicyNumber($policyNumber)) {
            Log::info('GlimsPolicyFallbackService: rejected invalid policy number format', [
                'input' => $rawPolicyNumber,
            ]);
            return null;
        }

        return $this->cachedLookup(
            cacheKey: "glims_fallback:policy_number:{$policyNumber}",
            type: 'policy_number',
            value: $policyNumber,
        );
    }

    /**
     * GLIMS-wide search by vehicle number, used when the vehicle tab's
     * local search comes back empty. Same treatment as search() — no
     * confirmed customer identity, so this returns a single unlinked
     * policy result, not a customer record.
     */
    public function searchByVehicle(string $rawVehicleNumber): ?array
    {
        $vehicleNumber = $this->normalizeVehicleNumber($rawVehicleNumber);

        if ($vehicleNumber === '') {
            return null;
        }

        return $this->cachedLookup(
            cacheKey: "glims_fallback:vehicle_number:{$vehicleNumber}",
            type: 'vehicle_number',
            value: $vehicleNumber,
        );
    }

    private function cachedLookup(string $cacheKey, string $type, string $value): ?array
    {
        return Cache::remember($cacheKey, now()->addMinutes(10), function () use ($type, $value) {
            $rawRows = $this->glims->getPolicyDetails($value, $type);

            if (empty($rawRows)) {
                return null;
            }

            $policyNumber = $rawRows[0]['policy_number'] ?? null;
            if (! $policyNumber) {
                return null;
            }

            $risks   = $this->glims->getRisksForPolicy($policyNumber);
            $first   = $this->mostRecentRow($rawRows); // ← was $rawRows[0]
            $isFleet = count($risks) > 1;

            return [
                'policy_id'           => null,
                'policy_number'       => $policyNumber,
                'business_class_name' => null,
                'product_name'        => $first['product'] ?? null,
                'vehicle_number'      => $isFleet ? 'FLEET' : ($risks[0]['risk_ref_no'] ?? ' '),
                'status'              => $this->deriveStatus($first['expiry_date'] ?? null),
                'start_date'          => $this->formatDate($first['start_date'] ?? null),
                'end_date'            => $this->formatDate($first['expiry_date'] ?? null),
                'renewal_date'        => null,
                'customer_name'       => null,
                'customer_code'       => null,
                'is_fleet'            => $isFleet,
                'risks'               => $this->stripFinancials($risks),
            ];
        });
    }

    /**
     * Pick the most recent row across all raw detail rows, so policy-level
     * fields (status, product, dates) are consistent regardless of API
     * row ordering or which entry point (policy_number vs vehicle_number)
     * triggered the lookup. Mirrors GlimsRiskResolver's per-vehicle logic,
     * applied at the whole-policy level.
     */
    private function mostRecentRow(array $rawRows): array
    {
        return collect($rawRows)
            ->sortByDesc(function ($row) {
                try {
                    return Carbon::createFromFormat('d/m/Y', $row['expiry_date'] ?? '')->timestamp;
                } catch (\Exception $e) {
                    return 0;
                }
            })
            ->first();
    }

    private function deriveStatus(?string $expiryDateRaw): string
    {
        if (! $expiryDateRaw) {
            return 'unknown';
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $expiryDateRaw)->isPast() ? 'expired' : 'active';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }

    private function formatDate(?string $raw): ?string
    {
        if (! $raw) {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $raw)->format('M d, Y');
        } catch (\Exception $e) {
            return $raw;
        }
    }

    private function stripFinancials(array $risks): array
    {
        return collect($risks)->map(function ($risk) {
            unset($risk['sum_insured'], $risk['total_premium']);
            return $risk;
        })->values()->toArray();
    }

    public function logUnlinkedView(Agent $agent, string $policyNumber): void
    {
        Log::info('Agent viewed unlinked GLIMS policy', [
            'agent_id'      => $agent->id,
            'policy_number' => $policyNumber,
        ]);
    }
}
