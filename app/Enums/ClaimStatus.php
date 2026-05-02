<?php
namespace App\Enums;

enum ClaimStatus: string {
    const INCOMING     = 'incoming';
    const UNDER_REVIEW = 'under_review';
    const PENDING_INFO = 'pending_info';
    const IN_PROGRESS  = 'in_progress';
    const APPROVED     = 'approved';
    const REJECTED     = 'rejected';
    const CLOSED       = 'closed';

    public static function all(): array
    {
        return [
            self::INCOMING,
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
            self::INCOMING     => 'Incoming',
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
            self::INCOMING     => 'blue',
            self::UNDER_REVIEW => 'indigo',
            self::PENDING_INFO => 'amber',
            self::IN_PROGRESS  => 'purple',
            self::APPROVED     => 'green',
            self::REJECTED     => 'red',
            self::CLOSED       => 'gray',
        ];
    }

    public static function badge(string $status): array
    {
        $labels = self::labels();
        $colors = self::colors();

        $colorMap = [
            'blue'   => 'bg-blue-50 text-blue-700 border-blue-100',
            'indigo' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
            'amber'  => 'bg-amber-50 text-amber-700 border-amber-100',
            'purple' => 'bg-purple-50 text-purple-700 border-purple-100',
            'green'  => 'bg-green-50 text-green-700 border-green-100',
            'red'    => 'bg-red-50 text-red-700 border-red-100',
            'gray'   => 'bg-gray-50 text-gray-700 border-gray-100',
        ];

        return [
            'label' => $labels[$status] ?? ucfirst($status),
            'class' => $colorMap[$colors[$status] ?? 'gray'],
        ];
    }
}
