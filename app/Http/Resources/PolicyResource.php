<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $raw   = $this->raw_payload ?? [];
        $risks = $this->extractRisks($raw);

        // Vehicle number: show 'FLEET' for multiple risks, plate for single, empty for none
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
            'risks'               => $risks,
        ];
    }

    private function extractRisks(array $raw): array
    {
        // ── New rich format (has 'risks' key from policy-search sync) ─────────
        if (! empty($raw['risks'])) {
            return collect($raw['risks'])
                ->map(fn($risk) => [
                    'id'                     => $risk['id'] ?? null, // ← add this
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

        // ── Old format (array at index 0, basic fields only) ──────────────────
        $entry = is_array($raw[0] ?? null) ? $raw[0] : $raw;
        $plate = $entry['vehicle_number'] ?? null;

        if (! $plate) {
            return [];
        }

        return [[
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
