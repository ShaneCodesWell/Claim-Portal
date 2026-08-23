<?php

namespace App\Models;

use App\Enums\LiabilityGuideStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiabilityGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'claim_id',
        'status',
        'completed_by',
        'completed_at',
        'data',
    ];

    protected $casts = [
        'data'         => 'array',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LiabilityGuideActivity::class)->latest();
    }

    // Status helpers
    public function isEditable(): bool
    {
        return LiabilityGuideStatus::isEditable($this->status);
    }

    public function isCompleted(): bool
    {
        return $this->status === LiabilityGuideStatus::COMPLETED;
    }

    // The most recent manager decision, read off the activity log
    // rather than a single column, since decisions can be revised.
    public function latestManagerDecision(): ?LiabilityGuideActivity
    {
        return $this->activities
            ->firstWhere('action', LiabilityGuideActivity::ACTION_MANAGER_DECIDED);
    }

    // Actions
    public function complete(User $by, ?string $note = null): void
    {
        $this->update([
            'status'       => LiabilityGuideStatus::COMPLETED,
            'completed_by' => $by->id,
            'completed_at' => now(),
        ]);

        $this->logActivity($by, LiabilityGuideActivity::ACTION_COMPLETED, $note);
    }

    public function reopen(User $by, ?string $note = null): void
    {
        $this->update(['status' => LiabilityGuideStatus::REOPENED]);

        $this->logActivity($by, LiabilityGuideActivity::ACTION_REOPENED, $note);
    }

    public function recordManagerDecision(User $manager, string $decision, ?string $note = null): void
    {
        // Store the decision text itself inside `data` (e.g. data['manager_decision'],
        // data['manager_signature']) via the controller/form update — this just logs
        // the event so a full history of decisions/revisions is preserved.
        $this->logActivity(
            $manager,
            LiabilityGuideActivity::ACTION_MANAGER_DECIDED,
            $note ?? $decision
        );
    }

    protected function logActivity(User $staff, string $action, ?string $note = null): LiabilityGuideActivity
    {
        return $this->activities()->create([
            'staff_id'              => $staff->id,
            'action'                => $action,
            'note'                  => $note,
            'claim_status_at_time'  => $this->claim?->status,
        ]);
    }
}
