<?php
namespace App\Models;

use App\Models\Claim;
use Illuminate\Database\Eloquent\Builder;
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

    // ── Query Scopes ──────────────────────────────────────────────────────────────

    public function scopeForCustomers(Builder $query, \Illuminate\Support\Collection $customerIds): Builder
    {
        return $query->whereIn('customer_id', $customerIds);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (blank($search)) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('policy_number', 'like', "%{$search}%")
                ->orWhere('product_name', 'like', "%{$search}%")
                ->orWhere('business_class_name', 'like', "%{$search}%");
        });
    }

    public function scopeOfType(Builder $query, ?string $type): Builder
    {
        if (blank($type)) {
            return $query;
        }

        return $query->where('business_class_name', $type);
    }

    public function scopeOfStatus(Builder $query, ?string $status): Builder
    {
        if (blank($status)) {
            return $query;
        }

        return $query->where('status', $status);
    }

    public function getVehicleNumberAttribute(): string
    {
        $raw = $this->raw_payload ?? [];

        if (! empty($raw['risks'])) {
            return count($raw['risks']) > 1
                ? 'FLEET'
                : ($raw['risks'][0]['risk_ref_no'] ?? '');
        }

        $entry = is_array($raw[0] ?? null) ? $raw[0] : $raw;

        return $entry['vehicle_number'] ?? '';
    }

    /**
     * Extract normalized vehicle data for a specific risk,
     * ready to pre-populate claim form fields.
     * Handles all three payload formats: Genova rich, GLIMS, Legacy.
     */
    public function vehicleFormData(?int $riskId = null): array
    {
        $raw   = $this->raw_payload ?? [];
        $risks = $raw['risks'] ?? [];

        if (! empty($risks)) {
            $risk = ($riskId !== null && isset($risks[$riskId]))
                ? $risks[$riskId]
                : reset($risks);

            return [
                'registration_no' => $risk['risk_ref_no'] ?? '',
                'make'            => $risk['vehicle_make'] ?? '',
                'model'           => $risk['vehicle_model'] ?? '',
                'year_of_make'    => $risk['vehicle_yr_manufacture'] ?? '',
                'vehicle_chassis' => $risk['vehicle_chassis_no'] ?? '',
                'vehicle_colour'  => $risk['vehicle_colour'] ?? '',
                'body_type'       => $risk['vehicle_body_type'] ?? '',
                'seats'           => $risk['seats'] ?? '',
                'cubic_capacity'  => $risk['cubic_capacity'] ?? '',
            ];
        }

        // Legacy Genova fallback
        $entry = is_array($raw[0] ?? null) ? $raw[0] : $raw;

        return [
            'registration_no' => $entry['vehicle_number'] ?? '',
            'make'            => '',
            'model'           => '',
            'year_of_make'    => '',
            'vehicle_chassis' => '',
            'vehicle_colour'  => '',
            'body_type'       => '',
            'seats'           => '',
            'cubic_capacity'  => '',
        ];
    }
}
