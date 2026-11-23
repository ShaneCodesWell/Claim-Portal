<?php

namespace App\Http\Resources\Api\V1;

use App\Support\ClaimFormSchema;
use App\Support\GlimsRiskResolver;
use App\Support\PolicyRisks;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Policy */
class PolicyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $type = ClaimFormSchema::fromClass($this->business_class_name);

        return [
            'id'              => $this->id,
            'policy_number'   => $this->policy_number,
            'business_class'  => $this->business_class_name,
            'claim_form_type' => $type,
            'product_name'    => $this->product_name,
            'status'          => ClaimFormSchema::coverable($this->resource) ? 'active' : 'expired',
            'start_date'      => $this->start_date?->toDateString(),
            'end_date'        => $this->end_date?->toDateString(),
            'renewal_date'    => $this->renewal_date?->toDateString(),
            'vehicle_number'  => trim((string) $this->vehicle_number) ?: null,
            'is_fleet'        => count(PolicyRisks::index($this->resource)) > 1,
            // Expired policies can still be claimed on for events inside the policy period
            'can_claim'       => $type !== null,
            'risks'           => $this->risks(),
        ];
    }

    private function risks(): array
    {
        $risks = $this->raw_payload['risks'] ?? [];

        if ($this->source === 'glims') {
            $risks = GlimsRiskResolver::resolve($risks);
        }

        return collect($risks)->map(fn($r) => [
            /** Send this as `risk_id` when filing a motor claim on a fleet policy. */
            'id'              => ($r['risk_ref_no'] ?? null) ?: null,
            'registration_no' => $r['risk_ref_no'] ?? null,
            'make'            => $r['vehicle_make'] ?? null,
            'model'           => $r['vehicle_model'] ?? null,
            'year'            => $r['vehicle_yr_manufacture'] ?? null,
            'chassis_no'      => $r['vehicle_chassis_no'] ?? null,
            'colour'          => $r['vehicle_colour'] ?? null,
            'body_type'       => $r['vehicle_body_type'] ?? null,
            'sum_insured'     => $r['sum_insured'] ?? null,
            'premium'         => $r['total_premium'] ?? null,
            'covers'          => collect($r['covers'] ?? [])
                ->map(fn($c) => is_array($c) ? ($c['covername'] ?? null) : $c)
                ->filter()->values()->all(),
        ])->values()->all();
    }
}
