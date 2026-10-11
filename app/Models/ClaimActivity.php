<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimActivity extends Model
{
    /** @use HasFactory<\Database\Factories\ClaimActivityFactory> */
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'user_id',
        'customer_id',
        'agent_id',
        'action',
        'note',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    /**
     * Display name of whoever performed the action.
     * Falls back to "System" only when no actor was recorded.
     */
    public function getActorNameAttribute(): string
    {
        return $this->user?->name
            ?? $this->customer?->name
            ?? $this->agent?->name
            ?? 'System';
    }
}
