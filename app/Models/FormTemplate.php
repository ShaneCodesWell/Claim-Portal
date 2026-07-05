<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormTemplate extends Model
{
    protected $fillable = [
        'product_type',
        'version',
        'status',
        'name',
        'description',
        'schema',
        'created_by',
        'published_at'
    ];

    protected $casts = [
        'schema' => 'array',
        'published_at' => 'datetime',
    ];

    public static function currentlyPublished(string $productType): self
    {
        return static::where('product_type', $productType)
            ->where('status', 'published')
            ->orderByDesc('version')
            ->firstOrFail();
    }
}