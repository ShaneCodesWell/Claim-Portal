<?php

namespace App\Support;

/**
 * Walks a form_templates.schema and resolves every `prefill_source`
 * (including inside `branches`) against a context array such as:
 *
 *   [
 *     'customer' => $customer,   // model or array
 *     'policy'   => $policy,     // model or array
 *     'risk'     => $risk,       // plain array, e.g. from raw_payload
 *   ]
 *
 * Returns a flat key => value array in the same shape your Blade
 * partials already expect ($f['registration_no'], $f['fullname'], ...).
 *
 * This replaces the hand-built $formData array in MotorFormController::index()
 * — instead of one controller per product manually listing every field,
 * the schema itself declares where each field's value comes from.
 */
class FormPrefillResolver
{
    public static function resolve(array $schema, array $context): array
    {
        $values = [];

        foreach ($schema['sections'] ?? [] as $section) {
            foreach ($section['fields'] ?? [] as $field) {
                self::resolveField($field, $context, $values);
            }
        }

        return $values;
    }

    protected static function resolveField(array $field, array $context, array &$values): void
    {
        if (!empty($field['prefill_source'])) {
            $values[$field['key']] = self::resolvePath($field['prefill_source'], $context);
        }

        // Branch sub-fields (e.g. driver_type -> branches.self -> driver_profile_name)
        foreach ($field['branches'] ?? [] as $branch) {
            foreach ($branch['fields'] ?? [] as $sub) {
                if (!empty($sub['prefill_source'])) {
                    $values[$sub['key']] = self::resolvePath($sub['prefill_source'], $context);
                }
            }
        }
    }

    /**
     * Resolve a dot path like "customer.name" or "risk.vehicle_make"
     * against the context array. Never throws — missing hops just
     * resolve to ''. Supports both array leaves (e.g. raw_payload risk
     * data) and Eloquent model leaves (e.g. $customer->name).
     */
    protected static function resolvePath(string $path, array $context): mixed
    {
        $segments = explode('.', $path);
        $root = array_shift($segments);

        $value = $context[$root] ?? null;
        if ($value === null) {
            return '';
        }

        foreach ($segments as $segment) {
            if (is_array($value)) {
                $value = $value[$segment] ?? null;
            } elseif (is_object($value)) {
                $value = $value->{$segment} ?? null;
            } else {
                return '';
            }

            if ($value === null) {
                return '';
            }
        }

        return is_scalar($value) ? $value : '';
    }
}