<?php

namespace App\Models;

use App\Models\ClaimDraftDocument;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClaimDraft extends Model
{
    /** @use HasFactory<\Database\Factories\ClaimDraftFactory> */
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'policy_id',
        'risk_id',
        'claim_type',
        'form_data',
        'last_saved_at',
    ];

    protected $casts = [
        'form_data'     => 'array',
        'last_saved_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDraftDocument::class);
    }
}
