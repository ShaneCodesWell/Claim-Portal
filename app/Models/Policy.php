<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    protected $fillable = [
        'customer_id',
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
        'raw_payload' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'effective_date' => 'date',
        'renewal_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

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
        if (!$this->end_date) return 'unknown';
        
        $daysUntilExpiry = now()->startOfDay()->diffInDays($this->end_date->startOfDay(), false);
        
        if ($daysUntilExpiry < 0) return 'expired';
        if ($daysUntilExpiry <= 30) return 'pending_renewal';
        return 'active';
    }
}