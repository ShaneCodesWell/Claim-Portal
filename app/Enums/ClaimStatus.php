<?php

namespace App\Enums;

enum ClaimStatus : string
{
    const SUBMITTED    = 'submitted';
    const UNDER_REVIEW = 'under_review';
    const PENDING_INFO = 'pending_info';
    const IN_PROGRESS  = 'in_progress';
    const APPROVED     = 'approved';
    const REJECTED     = 'rejected';
    const CLOSED       = 'closed';

    public static function all(): array
    {
        return [
            self::SUBMITTED,
            self::UNDER_REVIEW,
            self::PENDING_INFO,
            self::IN_PROGRESS,
            self::APPROVED,
            self::REJECTED,
            self::CLOSED,
        ];
    }

    public static function terminal(): array
    {
        return [self::APPROVED, self::REJECTED, self::CLOSED];
    }

    public static function labels(): array
    {
        return [
            self::SUBMITTED    => 'Submitted',
            self::UNDER_REVIEW => 'Under Review',
            self::PENDING_INFO => 'Pending Information',
            self::IN_PROGRESS  => 'In Progress',
            self::APPROVED     => 'Approved',
            self::REJECTED     => 'Rejected',
            self::CLOSED       => 'Closed',
        ];
    }

    public static function colors(): array
    {
        return [
            self::SUBMITTED    => 'blue',
            self::UNDER_REVIEW => 'indigo',
            self::PENDING_INFO => 'amber',
            self::IN_PROGRESS  => 'purple',
            self::APPROVED     => 'green',
            self::REJECTED     => 'red',
            self::CLOSED       => 'gray',
        ];
    }
}
