<?php
namespace App\Models;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Policy extends Model
{
    protected $fillable = [
        'customer_id',
        'source',
        'external_policy_id',
        'policy_number',
        'product_id',
        'product_name',
        'business_class_id',
        'business_class_name',
        'start_date',
        'end_date',
        'effective_date',
        'renewal_date',
        'status',
        'raw_payload',
        'last_synced_at',
    ];

    protected $casts = [
        'raw_payload'    => 'array',
        'start_date'     => 'date',
        'end_date'       => 'date',
        'effective_date' => 'date',
        'renewal_date'   => 'date',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(Claim::class);
    }

    // Source helpers
    public function isFromGenova(): bool
    {
        return $this->source === 'genova';
    }

    public function isFromGlims(): bool
    {
        return $this->source === 'glims';
    }

    public function isManual(): bool
    {
        return $this->source === 'manual';
    }

    // Status helpers
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function isPendingRenewal(): bool
    {
        return $this->status === 'pending_renewal';
    }

    //Auto calculate status based on end_date
    protected static function booted()
    {
        static::saving(function ($policy) {
            // Auto-calculate status before saving
            if ($policy->end_date) {
                $policy->status = $policy->calculateStatus();
            }
        });
    }

    public function calculateStatus(): string
    {
        if (! $this->end_date) {
            return 'unknown';
        }

        $daysUntilExpiry = now()->startOfDay()->diffInDays(
            $this->end_date->startOfDay(), false
        );

        if ($daysUntilExpiry < 0) {
            return 'expired';
        }

        if ($daysUntilExpiry <= 30) {
            return 'pending_renewal';
        }

        return 'active';
    }
}
