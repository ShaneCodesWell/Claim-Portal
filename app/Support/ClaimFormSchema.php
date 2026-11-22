<?php

namespace App\Support;

use App\Models\Policy;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class ClaimFormSchema
{
    public static function types(): array
    {
        return array_values(array_diff(array_keys(config('claim_forms')), ['documents']));
    }

    public static function has(?string $type): bool
    {
        return $type !== null && in_array($type, self::types(), true);
    }

    /** "GENERAL ACCIDENT" -> general_accident, or null when the class has no form. */
    public static function fromClass(?string $class): ?string
    {
        $type = str_replace(' ', '_', strtolower(trim((string) $class)));

        return self::has($type) ? $type : null;
    }

    public static function definition(string $type): array
    {
        return config("claim_forms.$type");
    }

    /** In force today (display only). Claims are judged by the event date, not by this. */
    public static function coverable(Policy $policy): bool
    {
        return ! $policy->end_date || $policy->end_date->gte(today());
    }

    // 1. rules(): add a $partial flag (for edits, required fields only apply when sent)
    public static function rules(string $type, bool $partial = false): array
    {
        $rules = ['form_data' => ['required', 'array']];

        foreach (self::definition($type)['fields'] as $name => $f) {
            $head = $f['required']
                ? ($partial ? ['sometimes', 'required'] : ['required'])
                : (isset($f['conditional']) ? [$f['conditional'], 'nullable'] : ['nullable']);

            $rules["form_data.$name"] = array_merge($head, $f['rules']);

            foreach ($f['items'] ?? [] as $key => $itemRules) {
                $rules["form_data.$name.*.$key"] = array_merge(['nullable'], $itemRules);
            }
        }

        return $rules;
    }

    // 2. New: decode the stored JSON lists for API output
    public static function present(?string $type, array $formData): array
    {
        if (! self::has($type)) {
            return $formData;
        }

        foreach (self::definition($type)['fields'] as $name => $f) {
            if (($f['type'] ?? '') === 'list' && is_string($formData[$name] ?? null)) {
                $formData[$name] = json_decode($formData[$name], true) ?: [];
            }
        }

        return $formData;
    }

    /** Keys the client sent that this form neither defines nor fills from the server. */
    public static function unknownFields(string $type, array $formData): array
    {
        $def   = self::definition($type);
        $known = array_merge(array_keys($def['fields']), $def['server_fields'] ?? [], ['fullname', 'email', 'phone']);

        return array_values(array_map('strval', array_diff(array_keys($formData), $known)));
    }

    public static function clean(string $type, array $formData): array
    {
        return Arr::only($formData, array_keys(self::definition($type)['fields']));
    }

    /** Store values the way the web forms do: strings, "true"/"false", and JSON strings for lists. */
    public static function normalise(string $type, array $data): array
    {
        $fields = self::definition($type)['fields'];
        $out    = [];

        foreach ($data as $name => $value) {
            if ($value === null) {
                continue;
            }

            $f = $fields[$name] ?? [];

            $out[$name] = match ($f['type'] ?? 'string') {
                'list'  => json_encode(self::rows($value, array_keys($f['items'] ?? [])), JSON_UNESCAPED_UNICODE),
                'bool'  => filter_var($value, FILTER_VALIDATE_BOOLEAN) ? 'true' : 'false',
                default => is_scalar($value) ? (string) $value : json_encode($value),
            };
        }

        return $out;
    }

    private static function rows(mixed $rows, array $keys): array
    {
        return collect(is_array($rows) ? $rows : [])
            ->map(fn($row) => collect($keys)->mapWithKeys(
                fn($k) => [$k => (string) (is_array($row) ? ($row[$k] ?? '') : '')]
            )->all())
            ->filter(fn($row) => array_filter($row, fn($v) => $v !== ''))
            ->values()
            ->all();
    }

    /** Values the server owns; whatever the client sent for these is ignored. */
    public static function serverFields(string $type, Policy $policy, ?int $riskKey): array
    {
        return match ($type) {
            'motor' => Arr::only($policy->vehicleFormData($riskKey), ['registration_no', 'make', 'model', 'year_of_make']),
            'fire'  => ['policy_no' => (string) $policy->policy_number, 'renewal_date' => $policy->renewal_date?->toDateString() ?? ''],
            default => [],
        };
    }

    public static function eventDate(string $type, array $formData): ?Carbon
    {
        $field = self::definition($type)['event_field'] ?? null;
        $raw   = $field ? ($formData[$field] ?? null) : null;

        try {
            return $raw ? Carbon::parse($raw) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** The event must fall inside the policy period (and inside the optional reporting window). */
    public static function periodError(Policy $policy, Carbon $event, bool $applyWindow = true): ?string
    {
        $day = $event->copy()->startOfDay();

        if ($policy->start_date && $day->lt($policy->start_date->copy()->startOfDay())) {
            return 'The event date is before this policy started (' . $policy->start_date->format('d M Y') . ').';
        }

        if ($policy->end_date && $day->gt($policy->end_date->copy()->startOfDay())) {
            return 'The event date is after this policy ended (' . $policy->end_date->format('d M Y') . ').';
        }

        $limit = config('api.report_window_days');

        if ($applyWindow && $limit !== null && $limit > 0 && abs($day->diffInDays(today())) > $limit) {
            return "Claims must be reported within {$limit} days of the event.";
        }

        return null;
    }
}
