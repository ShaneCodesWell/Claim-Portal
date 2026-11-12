<?php

namespace App\Enums;

class ClaimActivityAction
{
    public static function map(): array
    {
        return [
            'submitted'          => ['icon' => 'fa-file-alt',        'color' => 'bg-blue-100 text-blue-600'],
            'staff_initiated'    => ['icon' => 'fa-user-edit',       'color' => 'bg-blue-100 text-blue-600'],
            'assigned'           => ['icon' => 'fa-user-check',      'color' => 'bg-indigo-100 text-indigo-600'],
            'reassigned'         => ['icon' => 'fa-exchange-alt',    'color' => 'bg-purple-100 text-purple-600'],
            'status_changed'     => ['icon' => 'fa-sync-alt',        'color' => 'bg-gray-100 text-gray-600'],
            'info_requested'     => ['icon' => 'fa-question-circle', 'color' => 'bg-amber-100 text-amber-600'],
            'info_provided'      => ['icon' => 'fa-check-circle',    'color' => 'bg-green-100 text-green-600'],
            'documents_uploaded' => ['icon' => 'fa-paperclip',       'color' => 'bg-blue-100 text-blue-600'],
            'form_updated'       => ['icon' => 'fa-edit',            'color' => 'bg-green-100 text-green-600'],
            'note_added'         => ['icon' => 'fa-sticky-note',     'color' => 'bg-yellow-100 text-yellow-600'],
            'sent_to_survey'     => ['icon' => 'fa-search-location', 'color' => 'bg-cyan-100 text-cyan-600'],
            'surveyor_assigned'  => ['icon' => 'fa-user-tag',        'color' => 'bg-cyan-100 text-cyan-600'],
            'survey_completed'   => ['icon' => 'fa-clipboard-check', 'color' => 'bg-teal-100 text-teal-600'],
            'sent_to_committee'  => ['icon' => 'fa-gavel',           'color' => 'bg-orange-100 text-orange-600'],
            'committee_decision' => ['icon' => 'fa-balance-scale',   'color' => 'bg-orange-100 text-orange-600'],
            'finalized'          => ['icon' => 'fa-check-double',    'color' => 'bg-emerald-100 text-emerald-600'],
            'cancelled'          => ['icon' => 'fa-ban',             'color' => 'bg-red-100 text-red-600'],
        ];
    }

    public static function meta(string $action): array
    {
        return self::map()[$action] ?? ['icon' => 'fa-circle', 'color' => 'bg-gray-100 text-gray-500'];
    }

    public static function label(string $action): string
    {
        return ucfirst(str_replace('_', ' ', $action));
    }
}
