<?php

namespace App\Enums;

class LiabilityGuideStatus
{
    const DRAFT     = 'draft';
    const COMPLETED = 'completed';
    const REOPENED  = 'reopened';

    public static function all(): array
    {
        return [
            self::DRAFT,
            self::COMPLETED,
            self::REOPENED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::DRAFT     => 'Draft',
            self::COMPLETED => 'Completed',
            self::REOPENED  => 'Reopened',
        ];
    }

    // Statuses in which the main staff form is editable
    public static function editable(): array
    {
        return [self::DRAFT, self::REOPENED];
    }

    public static function isEditable(string $status): bool
    {
        return in_array($status, self::editable());
    }

    public static function badge(string $status): array
    {
        $labels = self::labels();

        $colorMap = [
            self::DRAFT     => 'bg-gray-50 text-gray-700 border-gray-100',
            self::COMPLETED => 'bg-green-50 text-green-700 border-green-100',
            self::REOPENED  => 'bg-amber-50 text-amber-700 border-amber-100',
        ];

        return [
            'label' => $labels[$status] ?? ucfirst($status),
            'class' => $colorMap[$status] ?? 'bg-gray-50 text-gray-700 border-gray-100',
        ];
    }
}
