<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiabilityGuideActivity extends Model
{
    use HasFactory;

    // Common action values — not enforced by the DB, just a shared vocabulary
    const ACTION_COMPLETED       = 'completed';
    const ACTION_REOPENED        = 'reopened';
    const ACTION_UPDATED         = 'updated';
    const ACTION_MANAGER_DECIDED = 'manager_decided';

    protected $fillable = [
        'liability_guide_id',
        'staff_id',
        'action',
        'note',
        'claim_status_at_time',
    ];

    public function liabilityGuide(): BelongsTo
    {
        return $this->belongsTo(LiabilityGuide::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}