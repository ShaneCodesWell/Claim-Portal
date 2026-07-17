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

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }
}
