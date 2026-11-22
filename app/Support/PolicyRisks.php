<?php

namespace App\Support;

use App\Models\Policy;

class PolicyRisks
{
    public static function norm(?string $plate): string
    {
        return mb_strtoupper(preg_replace('/[\s\-]+/', '', (string) $plate));
    }

    /** [normalised plate => raw payload key], first occurrence only. Works for list and object-keyed payloads. */
    public static function index(Policy $policy): array
    {
        $index = [];

        foreach (($policy->raw_payload['risks'] ?? []) as $key => $risk) {
            $plate = self::norm($risk['risk_ref_no'] ?? '');

            if ($plate !== '' && ! isset($index[$plate])) {
                $index[$plate] = (int) $key;
            }
        }

        return $index;
    }
}