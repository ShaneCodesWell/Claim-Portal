<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $raw   = $this->raw_payload ?? [];
        $risks = $this->extractRisks($raw, $this->source);

        // Vehicle number:
        //   > 1 risk  → 'FLEET'
        //   1 risk    → the plate number
        //   0 risks   → ' ' (non-motor policy, keep existing behaviour)
        $vehicleNumber = count($risks) > 1
            ? 'FLEET'
            : ($risks[0]['risk_ref_no'] ?? data_get($raw, '0.vehicle_number') ?? data_get($raw, 'vehicle_number') ?? ' ');

        return [
            'policy_id'           => $this->id,
            'policy_number'       => $this->policy_number,
            'business_class_name' => $this->business_class_name,
            'product_name'        => $this->product_name,
            'vehicle_number'      => $vehicleNumber,
            'status'              => $this->status,
            'start_date'          => optional($this->start_date)?->format('M d, Y'),
            'end_date'            => optional($this->end_date)?->format('M d, Y'),
            'renewal_date'        => optional($this->renewal_date)?->format('M d, Y'),
            'customer_name'       => $this->customer->name ?? null,
            'customer_code'       => $this->customer->external_customer_code ?? null,
            'customer_phone'      => $this->customer->phone ?? null,
            'customer_email'      => $this->customer->email ?? null,
            'source'              => $this->source,
            'risks'               => $risks,
        ];
    }

    /**
     * Extract a normalised risks array from raw_payload.
     *
     * Three formats are handled, determined by $source + payload shape:
     *
     *   1. Genova rich  — payload has a 'risks' key (from syncFromGenovaRich)
     *                     Full vehicle detail including covers.
     *
     *   2. GLIMS API    — payload has a 'risks' key (from syncFromGlims / GlimsApiService)
     *                     source === 'glims'. Same 'risks' key as Genova rich but
     *                     fewer vehicle fields (make/model etc. not in middleware yet).
     *
     *   3. Genova old   — array at index 0, basic fields only (legacy pre-rich sync)
     *                     Kept for backwards compatibility with existing DB records.
     */
    private function extractRisks(array $raw, ?string $source = null): array
    {
        // ── Format 1 & 2: 'risks' key present (both Genova rich and GLIMS API) ──
        if (! empty($raw['risks'])) {

            if ($source === 'glims') {
                return $this->extractGlimsRisks($raw['risks']);
            }

            // Genova rich format (default)
            return $this->extractGenovaRisks($raw['risks']);
        }

        // ── Format 3: Legacy Genova old format ────────────────────────────────
        return $this->extractLegacyRisks($raw);
    }

    // ── Risk extractors ───────────────────────────────────────────────────────

    /**
     * Genova rich format — full vehicle + covers detail.
     */
    private function extractGenovaRisks(array $risks): array
    {
        return collect($risks)
            ->map(fn($risk) => [
                'id'                     => $risk['id'] ?? null,
                'risk_ref_no'            => $risk['risk_ref_no'] ?? null,
                'vehicle_make'           => $risk['vehicle_make'] ?? null,
                'vehicle_model'          => $risk['vehicle_model'] ?? null,
                'vehicle_yr_manufacture' => $risk['vehicle_yr_manufacture'] ?? null,
                'vehicle_chassis_no'     => $risk['vehicle_chassis_no'] ?? null,
                'vehicle_colour'         => $risk['vehicle_colour'] ?? null,
                'vehicle_body_type'      => $risk['vehicle_body_type'] ?? null,
                'sum_insured'            => $risk['sum_insured'] ?? null,
                'total_premium'          => $risk['total_premium'] ?? null,
                'covers'                 => collect($risk['covers'] ?? [])
                    ->map(fn($cover) => ['covername' => $cover['covername'] ?? ''])
                    ->values()
                    ->toArray(),
            ])
            ->values()
            ->toArray();
    }

    /**
     * GLIMS API format — plate number confirmed, vehicle detail pending IT.
     *
     * The middleware currently returns: plate_number, sum_insured, premium, lob, product.
     * Vehicle make/model/chassis/colour are not yet in the response.
     * When IT adds those fields to the middleware, update normaliseRisk()
     * in GlimsApiService and the mapping here will pick them up automatically
     * via the '_raw' key.
     */
    private function extractGlimsRisks(array $risks): array
    {
        return collect($risks)
            ->map(fn($risk) => [
                'id'                     => null,
                'risk_ref_no'            => $risk['risk_ref_no'] ?? null,
                'vehicle_make'           => $risk['vehicle_make'] ?? null,
                'vehicle_model'          => $risk['vehicle_model'] ?? null,
                'vehicle_yr_manufacture' => $risk['vehicle_yr_manufacture'] ?? null,
                'vehicle_chassis_no'     => $risk['vehicle_chassis_no'] ?? null,
                'vehicle_colour'         => $risk['vehicle_colour'] ?? null,
                'vehicle_body_type'      => $risk['vehicle_body_type'] ?? null,
                'sum_insured'            => $risk['sum_insured'] ?? null,
                'total_premium'          => $risk['total_premium'] ?? null,
                'seats'                  => $risk['seats'] ?? null,
                'cubic_capacity'         => $risk['cubic_capacity'] ?? null,
                'usage'                  => $risk['usage'] ?? null,
                'covers'                 => $risk['covers'] ?? [],
            ])
            ->values()
            ->toArray();
    }

    /**
     * Legacy Genova format — single vehicle stored at raw[0] or raw root.
     * Kept for backwards compatibility with records synced before the rich sync.
     */
    private function extractLegacyRisks(array $raw): array
    {
        $entry = is_array($raw[0] ?? null) ? $raw[0] : $raw;
        $plate = $entry['vehicle_number'] ?? null;

        if (! $plate) {
            return [];
        }

        return [[
            'id'                     => null,
            'risk_ref_no'            => $plate,
            'vehicle_make'           => null,
            'vehicle_model'          => null,
            'vehicle_yr_manufacture' => null,
            'vehicle_chassis_no'     => null,
            'vehicle_colour'         => null,
            'vehicle_body_type'      => null,
            'sum_insured'            => null,
            'total_premium'          => null,
            'covers'                 => [],
        ]];
    }
}
