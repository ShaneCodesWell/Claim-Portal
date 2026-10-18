<?php

namespace App\Support;

class GlimsRiskResolver
{
    /**
     * Collapse GLIMS risk rows down to one entry per distinct vehicle.
     *
     * GLIMS sometimes returns multiple rows for the SAME physical vehicle
     * under one policy, e.g. an endorsement that re-rates the premium
     * appends a new transaction row rather than updating the existing one.
     * Without this, a single-vehicle policy with one endorsement gets
     * misread as a fleet (risk count > 1).
     *
     * "Same vehicle" = same risk_ref_no (plate) + same chassis number.
     * When duplicates exist for a vehicle, the transaction with the latest
     * `created` timestamp (in _raw) wins.
     *
     * The premium for a vehicle is its net_premium (falling back to
     * total_premium). On an endorsement row, total_premium is only that
     * transaction's change and can be negative (e.g. -3780), while net_premium
     * is the vehicle's actual current premium. This applies to single-row
     * vehicles too.
     *
     * Single source of truth for this logic, used by both:
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
                $latest = $group->count() === 1
                    ? $group->first()
                    : $group->sortByDesc(fn($r) => $r['_raw']['created'] ?? '')->first();

                $resolvedPremium = $latest['_raw']['net_premium'] ?? $latest['total_premium'] ?? null;

                return array_merge($latest, [
                    'total_premium' => $resolvedPremium,
                ]);
            })
            ->values()
            ->toArray();
    }

    /**
     * The GLIMS details endpoint can return only ONE vehicle of a fleet policy,
     * while the policy search lists all of them.
     *
     * Keeps every vehicle from the details ($richRisks) and adds the vehicles that
     * only the search knew about ($placeholderRisks). The figures on a search row
     * belong to the whole policy, not to that vehicle, so they are left empty for
     * the added vehicles instead of showing a wrong number.
     *
     * @param  array<int, array>  $richRisks         Risks from the details endpoint
     * @param  array<int, array>  $placeholderRisks  Risks built from the policy search rows
     * @return array<int, array>
     */
    public static function mergeMissingVehicles(array $richRisks, array $placeholderRisks): array
    {
        $key = fn($plate) => mb_strtoupper(preg_replace('/[\s\-]+/', '', (string) $plate));

        $have = collect($richRisks)
            ->map(fn($risk) => $key($risk['risk_ref_no'] ?? ''))
            ->filter()
            ->values()
            ->all();

        $merged = $richRisks;

        foreach ($placeholderRisks as $placeholder) {
            $plate = $key($placeholder['risk_ref_no'] ?? '');

            if ($plate === '' || in_array($plate, $have, true)) {
                continue;
            }

            $placeholder['sum_insured']     = null;
            $placeholder['total_premium']   = null;
            $placeholder['details_missing'] = true;

            $merged[] = $placeholder;
            $have[]   = $plate;
        }

        return $merged;
    }
}
