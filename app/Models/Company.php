<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'tagline',
        'email',
        'claims_email',
        'phone_primary',
        'phone_secondary',
        'phone_tertiary',
        'postal_address',
        'physical_address',
        'logo_path',
        'website',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
