<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'            => 'customer',
            'id'              => $this->id,
            'name'            => $this->name,
            'phone'           => $this->phone,
            'email'           => $this->email,
            'policies_synced' => $this->last_synced_at !== null,
            'last_synced_at'  => $this->last_synced_at?->toIso8601String(),
        ];
    }
}
