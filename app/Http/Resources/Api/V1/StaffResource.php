<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type'                => 'staff',
            'id'                  => $this->id,
            'name'                => $this->name,
            'email'               => $this->email,
            'role'                => $this->role,
            'is_admin'            => (bool) $this->is_admin,
            'is_committee_member' => (bool) $this->is_committee_member,
        ];
    }
}
