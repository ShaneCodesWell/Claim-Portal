<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PolicyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'policy_id'           => $this->id,
            'policy_number'       => $this->policy_number,
            'business_class_name' => $this->business_class_name,
            'product_name'        => $this->product_name,
            'vehicle_number'      => data_get($this->raw_payload, '0.vehicle_number') ?? data_get($this->raw_payload, 'vehicle_number'),
            'status'              => $this->status,
            'start_date'          => optional($this->start_date)?->format('M d, Y'),
            'end_date'            => optional($this->end_date)?->format('M d, Y'),
            'renewal_date'        => optional($this->renewal_date)?->format('M d, Y'),
            'customer_name'       => $this->customer->name ?? null,
            'customer_code'       => $this->customer->external_customer_code ?? null,
            'customer_phone'      => $this->customer->phone ?? null,
            'customer_email'      => $this->customer->email ?? null,
        ];
    }
}
