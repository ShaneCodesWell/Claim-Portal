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
        'status',
        'raw_payload',
        'last_synced_at',
    ];

    protected $casts = [
        'raw_payload' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}