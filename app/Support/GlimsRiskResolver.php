<?php

namespace App\Support;

class GlimsRiskResolver
{
    /**
     * Collapse GLIMS risk rows down to one entry per distinct vehicle.
     *
     * GLIMS sometimes returns multiple rows for the SAME physical vehicle
     * under one policy — e.g. an endorsement that re-rates the premium
     * appends a new transaction row rather than updating the existing one.
     * Without this, a single-vehicle policy with one endorsement gets
     * misread as a fleet (risk count > 1).
     *
     * "Same vehicle" = same risk_ref_no (plate) + same chassis number.
     * When duplicates exist for a vehicle, the transaction with the latest
     * `created` timestamp (in _raw) wins, and its net_premium (falling back
     * to total_premium) becomes the resolved premium — this is the actual
     * post-endorsement premium, not a sum of the transaction deltas.
     *
     * Single source of truth for this logic — used by both:
     *   - GlimsApiService::getRisksForPolicy()   (sync-time: what gets stored)
     *   - PolicyResource::extractGlimsRisks()    (display-time: what gets shown)
     *
     * @param  array<int, array>  $risks  Normalised risk rows (from GlimsApiService::normaliseDetailToRisk())
     * @return array<int, array>          One entry per distinct vehicle
     */
    public static function resolve(array $risks): array
    {
        return collect($risks)
            ->groupBy(function ($risk) {
                $plate   = $risk['risk_ref_no'] ?? 'unknown';
                $chassis = $risk['vehicle_chassis_no'] ?? 'unknown';
                return $plate . '|' . $chassis;
            })
            ->map(function ($group) {
                if ($group->count() === 1) {
                    return $group->first();
                }

                $latest = $group->sortByDesc(fn($r) => $r['_raw']['created'] ?? '')->first();

                $resolvedPremium = $latest['_raw']['net_premium'] ?? $latest['total_premium'] ?? null;

                return array_merge($latest, [
                    'total_premium' => $resolvedPremium,
                ]);
            })
            ->values()
            ->toArray();
    }
}