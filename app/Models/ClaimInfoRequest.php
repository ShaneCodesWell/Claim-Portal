<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClaimInfoRequest extends Model
{
    public const PENDING   = 'pending';
    public const RESPONDED = 'responded';
    public const CANCELLED = 'cancelled';

    public const SMS_QUEUED = 'queued';
    public const SMS_SENT   = 'sent';
    public const SMS_FAILED = 'failed';

    public const NOTE_MAX = 480; // about 4 SMS parts once the fixed text is added (153 chars per part)

    // Static closing line. Change the wording here and the SMS and the staff preview both update.
    public const SMS_FOOTER = 'Visit eclaim.vanguardassurance.net to respond. - Vanguard Assurance';

    public static function smsFirstName(?string $name): string
    {
        return Str::title(Str::lower(Str::before(trim($name ?: 'Customer'), ' ')));
    }

    public static function smsMessage(Claim $claim, string $note): string
    {
        // return 'Hi ' . self::smsFirstName($claim->customer?->name)
        //     . ", we need more info on claim {$claim->claim_number}:\n"
        //     . "\"{$note}\"\n"
        //     . self::SMS_FOOTER;

        return 'Hi ' . self::smsFirstName($claim->customer?->name)
            . ",\n"
            . "\"{$note}\"\n"
            . self::SMS_FOOTER;
    }

    protected $fillable = [
        'claim_id',
        'requested_by',
        'note',
        'previous_status',
        'status',
        'response_note',
        'responded_at',
        'sms_status',
        'sms_sent_at',
        'sms_error',
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
