<?php

namespace App\Support;

use App\Enums\ClaimStatus;

class ApiDocs
{
    public static function description(): string
    {
        $overview = is_file(base_path('docs/api/overview.md'))
            ? file_get_contents(base_path('docs/api/overview.md'))
            : '';

        return $overview . "\n\n" . self::statuses() . "\n\n" . self::forms();
    }

    private static function statuses(): string
    {
        $meaning = [
            'submitted'        => 'Received and waiting to be picked up by staff. The customer can still cancel.',
            'under_review'     => 'Assigned to a handler and being reviewed.',
            'pending_info'     => 'Waiting for the customer to provide information (`action_required` is true).',
            'in_progress'      => 'Being worked on by staff after a form update.',
            'under_survey'     => 'With the survey team for assessment.',
            'survey_completed' => 'The survey is done and the claim is back with the handler.',
            'committee_review' => 'With the claims committee for a decision.',
            'approved'         => 'Approved by the committee. Waiting to be finalized.',
            'rejected'         => 'Rejected by the committee. The customer has been notified. Waiting to be closed.',
            'closed'           => 'Finished. Final state.',
            'cancelled'        => 'Cancelled. Hidden from the customer list unless `status=cancelled` is requested.',
        ];

        $rows = collect(ClaimStatus::labels())
            ->map(fn($label, $status) => "| `{$status}` | {$label} | " . ($meaning[$status] ?? '') . ' |')
            ->implode("\n");

        return "## Claim statuses\n\n| Status | Label | Meaning |\n|---|---|---|\n" . $rows;
    }

    private static function forms(): string
    {
        $out = "## Claim forms\n\nThese are the `form_data` fields for `POST /claims`. "
            . "`GET /claim-forms/{type}` returns the same information as JSON.\n";

        foreach (ClaimFormSchema::types() as $type) {
            $def = ClaimFormSchema::definition($type);

            $out .= "\n### {$def['label']} (`{$type}`)\n\n";
            $out .= "Event date field: `{$def['event_field']}`. ";

            if (! empty($def['server_fields'])) {
                $out .= 'Filled from the policy (do not send): `' . implode('`, `', $def['server_fields']) . '`. ';
            }

            if (! empty($def['notes'])) {
                $out .= $def['notes'];
            }

            $out .= "\n\n| Field | Type | Required | Notes |\n|---|---|---|---|\n";

            foreach ($def['fields'] as $name => $f) {
                $required = $f['required'] ? 'Yes' : 'No';

                if (
                    ! empty($f['conditional'])
                    && preg_match('/^required_if:form_data\.([^,]+),(.+)$/', $f['conditional'], $m)
                ) {
                    $required = "When `{$m[1]}` is " . implode(' or ', array_map(fn($v) => "`{$v}`", explode(',', $m[2])));
                }

                $notes = [$f['label']];

                if (! empty($f['options'])) {
                    $notes[] = 'One of: ' . implode(', ', array_map(fn($o) => "`{$o}`", $f['options']));
                }

                if (! empty($f['items'])) {
                    $notes[] = 'List of objects with: ' . implode(', ', array_map(fn($k) => "`{$k}`", array_keys($f['items'])));
                }

                $out .= "| `{$name}` | {$f['type']} | {$required} | " . implode('. ', $notes) . " |\n";
            }
        }

        return $out;
    }
}
