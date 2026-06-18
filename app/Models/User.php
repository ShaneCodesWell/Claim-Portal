<?php
namespace App\Models;

use App\Enums\UserRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
        'is_committee_member',
        'role',
        'branch_id',
        'department_id',
        'phone',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at'   => 'datetime',
            'password'            => 'hashed',
            'is_admin'            => 'boolean',
            'is_committee_member' => 'boolean',
        ];
    }

    // helper Functions
    public function isAdmin(): bool
    {
        return $this->is_admin || $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function isClaimHead(): bool
    {
        return $this->role === 'claim_head';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function assignedClaims(): HasMany
    {
        return $this->hasMany(Claim::class, 'assigned_to');
    }

    public function isSurveyor(): bool
    {
        return $this->role === UserRole::SURVEYOR->value;
    }

    public function isCommitteeMember(): bool
    {
        return (bool) $this->is_committee_member;
    }

    public function isClaimsAdjuster(): bool
    {
        return $this->role === UserRole::CLAIMS_ADJUSTER->value;
    }

    // New relationships
    public function surveyedClaims(): HasMany
    {
        return $this->hasMany(Claim::class, 'surveyed_by');
    }

    public function committeeDecisions(): HasMany
    {
        return $this->hasMany(Claim::class, 'committee_decided_by');
    }
}
