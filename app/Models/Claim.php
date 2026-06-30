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
        'amount',
        'form_data',
        'submitted_at',
        'surveyed_by',
        'surveyed_at',
        'survey_completed_at',
        'survey_notes',
        'committee_review_at',
        'committee_notes',
        'committee_decided_by',
        'committee_decided_at',
        'finalized_by',
        'finalized_at',
        'initiated_by_staff',
        'initiated_by',
    ];

    protected $casts = [
        'form_data'            => 'array',
        'assigned_at'          => 'datetime',
        'submitted_at'         => 'datetime',
        'surveyed_at'          => 'datetime',
        'survey_completed_at'  => 'datetime',
        'committee_review_at'  => 'datetime',
        'committee_decided_at' => 'datetime',
        'finalized_at'         => 'datetime',
        'initiated_by_staff'   => 'boolean',
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

    public function surveyor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'surveyed_by');
    }

    public function committeeDecidedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'committee_decided_by');
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

    public function isUnderSurvey(): bool
    {
        return $this->status === ClaimStatus::UNDER_SURVEY;
    }

    public function isSurveyCompleted(): bool
    {
        return $this->status === ClaimStatus::SURVEY_COMPLETED;
    }

    public function isCommitteeReview(): bool
    {
        return $this->status === ClaimStatus::COMMITTEE_REVIEW;
    }

    public function isEditable(): bool
    {
        return ClaimStatus::isEditable($this->status);
    }

    public function finalizedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'finalized_by');
    }

    public function isFinalizableBy(User $user): bool
    {
        return $user->isAdmin()
        || $user->isClaimHead()
        || $this->assigned_to === $user->id;
    }

    // Other helpers
    public static function generateClaimNumber(): string
    {
        $year     = now()->year;
        $latest   = self::whereYear('created_at', $year)->count();
        $sequence = str_pad($latest + 1, 6, '0', STR_PAD_LEFT);

        return "CLM-{$year}-{$sequence}";
    }

    public function getRegistrationNumberAttribute(): ?string
    {
        return $this->form_data['registration_no'] ?? null;
    }

    public function initiatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'initiated_by');
    }
}
