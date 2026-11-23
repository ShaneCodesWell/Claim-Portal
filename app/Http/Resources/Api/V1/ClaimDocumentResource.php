<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\ClaimDocument */
class ClaimDocumentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->original_name,
            'mime_type'   => $this->mime_type,
            'size_bytes'  => $this->file_size,
            'uploaded_by' => $this->uploaded_by_customer_id ? 'customer' : ($this->uploaded_by_agent_id ? 'agent' : 'staff'),
            'uploaded_at' => $this->created_at?->toIso8601String(),
            'url'         => route('api.v1.documents.show', $this->id),
        ];
    }
}