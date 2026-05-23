<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Agent extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\AgentFactory> */
    use HasFactory;

    protected $fillable = [
        'partner_code',
        'name',
        'email',
        'phone',
        'password',
        'last_synced_at',
        'local_password',
        'local_password_set_at',
        'user_category',
        'sub_user_category',
        'gender',
        'league',
    ];

    protected $hidden = [
        'password',
        'local_password',
        'remember_token',
    ];

    protected $casts = [
        'password'              => 'hashed',
        'last_synced_at'        => 'datetime',
        'local_password_set_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
