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
        return $this->belongsTo(User::class);
    }
}
