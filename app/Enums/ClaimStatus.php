<?php
namespace App\Enums;

enum ClaimStatus: string {
    const SUBMITTED        = 'submitted';
    const UNDER_REVIEW     = 'under_review';
    const PENDING_INFO     = 'pending_info';
    const IN_PROGRESS      = 'in_progress';
    const UNDER_SURVEY     = 'under_survey';
    const SURVEY_COMPLETED = 'survey_completed';
    const COMMITTEE_REVIEW = 'committee_review';
    const APPROVED         = 'approved';
    const REJECTED         = 'rejected';
    const CLOSED           = 'closed';
    const CANCELLED        = 'cancelled';

    public static function all(): array
    {
        return [
            self::SUBMITTED,
            self::UNDER_REVIEW,
            self::PENDING_INFO,
            self::IN_PROGRESS,
            self::UNDER_SURVEY,
            self::SURVEY_COMPLETED,
            self::COMMITTEE_REVIEW,
            self::APPROVED,
            self::REJECTED,
            self::CLOSED,
            self::CANCELLED,
        ];
    }

    public static function terminal(): array
    {
        return [self::APPROVED, self::REJECTED, self::CLOSED];
    }

    public static function labels(): array
    {
        return [
            self::SUBMITTED        => 'Submitted',
            self::UNDER_REVIEW     => 'Under Review',
            self::PENDING_INFO     => 'Pending Info',
            self::IN_PROGRESS      => 'In Progress', self::UNDER_SURVEY => 'Under Survey',
            self::SURVEY_COMPLETED => 'Survey Completed',
            self::COMMITTEE_REVIEW => 'Committee Review',
            self::APPROVED         => 'Approved',
            self::REJECTED         => 'Rejected',
            self::CLOSED           => 'Closed',
            self::CANCELLED        => 'Cancelled',
        ];
    }

    public static function colors(): array
    {
        return [
            self::SUBMITTED        => 'blue',
            self::UNDER_REVIEW     => 'indigo',
            self::PENDING_INFO     => 'amber',
            self::IN_PROGRESS      => 'purple',
            self::UNDER_SURVEY     => 'cyan',
            self::SURVEY_COMPLETED => 'teal',
            self::COMMITTEE_REVIEW => 'orange',
            self::APPROVED         => 'green',
            self::REJECTED         => 'red',
            self::CLOSED           => 'gray',
            self::CANCELLED        => 'gray',
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
            'cyan'   => 'bg-cyan-50 text-cyan-700 border-cyan-100',
            'teal'   => 'bg-teal-50 text-teal-700 border-teal-100',
            'orange' => 'bg-orange-50 text-orange-700 border-orange-100',
            'green'  => 'bg-green-50 text-green-700 border-green-100',
            'red'    => 'bg-red-50 text-red-700 border-red-100',
            'gray'   => 'bg-gray-50 text-gray-700 border-gray-100',
        ];

        return [
            'label' => $labels[$status] ?? ucfirst($status),
            'class' => $colorMap[$colors[$status] ?? 'gray'],
        ];
    }

    public static function cancellable(): array
    {
        return [
            self::SUBMITTED,
            self::IN_PROGRESS,
            self::PENDING_INFO,
            self::UNDER_REVIEW,
        ];
    }

    public static function incoming(): array
    {
        return [
            self::SUBMITTED,
        ];
    }

    public static function editable(): array
    {
        return [
            self::SUBMITTED,
            self::IN_PROGRESS,
            self::UNDER_REVIEW,
            self::PENDING_INFO,
        ];
    }

    public static function isEditable(string $status): bool
    {
        return in_array($status, self::editable());
    }

    // Convenience groupings
    public static function surveyStages(): array
    {
        return [self::UNDER_SURVEY, self::SURVEY_COMPLETED];
    }

    public static function committeeStages(): array
    {
        return [self::COMMITTEE_REVIEW];
    }
}
