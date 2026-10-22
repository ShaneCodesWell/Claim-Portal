<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimInfoRequest extends Model
{
    public const PENDING   = 'pending';
    public const RESPONDED = 'responded';
    public const CANCELLED = 'cancelled';
 
    public const SMS_QUEUED = 'queued';
    public const SMS_SENT   = 'sent';
    public const SMS_FAILED = 'failed';
 
    protected $fillable = [
        'claim_id', 'requested_by', 'note', 'previous_status',
        'status', 'response_note', 'responded_at',
        'sms_status', 'sms_sent_at', 'sms_error',
    ];
 
    protected $casts = [
        'responded_at' => 'datetime',
        'sms_sent_at'  => 'datetime',
    ];
 
    public function claim(): BelongsTo
    {
        return $this->belongsTo(Claim::class);
    }
 
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
 
    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::PENDING);
    }
 
    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }
}
