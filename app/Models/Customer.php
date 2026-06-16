<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Customer extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'external_customer_id',
        'external_customer_code',
        'name',
        'email',
        'phone',
        'password',
        'last_synced_at',
        'local_password',
        'local_password_set_at',
        'sources',
        'raw_payload',
    ];

    protected $casts = [
        'sources'               => 'array',
        'raw_payload'           => 'array',
        'last_synced_at'        => 'datetime',
        'local_password_set_at' => 'datetime',
    ];

    public function policies()
    {
        return $this->hasMany(Policy::class);
    }
}
