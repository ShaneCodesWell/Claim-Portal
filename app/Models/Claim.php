<?php
namespace App\Models;

use App\Enums\ClaimStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Claim extends Model
{
    /** @use HasFactory<\Database\Factories\ClaimFactory> */
    use HasFactory;

    protected $fillable = [
        'claim_number',
        'customer_id',
        'policy_id',
        'branch_id',
        'assigned_to',
        'assigned_by',
        'assigned_at',
        'claim_type',
        'source',
        'status',
        'form_data',
        'submitted_at',
    ];

    protected $casts = [
        'form_data'    => 'array',
        'assigned_at'  => 'datetime',
        'submitted_at' => 'datetime',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function policy(): BelongsTo
    {
        return $this->belongsTo(Policy::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(ClaimActivity::class)->latest();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ClaimDocument::class);
    }

    // Status helpers
    public function isTerminal(): bool
    {
        return in_array($this->status, ClaimStatus::terminal());
    }

    public function isPendingInfo(): bool
    {
        return $this->status === ClaimStatus::PENDING_INFO;
    }

    public function isAssigned(): bool
    {
        return ! is_null($this->assigned_to);
    }

    // Generate claim number
    // public static function generateClaimNumber(): string
    // {
    //     $year     = now()->year;
    //     $latest   = self::whereYear('created_at', $year)->lockForUpdate()->count();
    //     $sequence = str_pad($latest + 1, 6, '0', STR_PAD_LEFT);
    //     return "CLM-{$year}-{$sequence}";
    // }
    public static function generateClaimNumber(): string
    {
        $year     = now()->year;
        $latest   = self::whereYear('created_at', $year)->count();
        $sequence = str_pad($latest + 1, 6, '0', STR_PAD_LEFT);

        return "CLM-{$year}-{$sequence}";
    }
}
