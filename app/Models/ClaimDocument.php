<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimDocument extends Model
{
    /** @use HasFactory<\Database\Factories\ClaimDocumentFactory> */
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'uploaded_by',
        'uploaded_by_customer_id',
        'uploaded_by_agent_id',
        'type',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function uploadedByCustomer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'uploaded_by_customer_id');
    }

    public function uploadedByAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'uploaded_by_agent_id');
    }
}
