<?php

namespace App\Support;

class PhoneNormalizer
{
    public static function normalizeGhanaPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return '0' . substr($digits, 3);
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return $digits;
        }

        if (strlen($digits) === 9) {
            return '0' . $digits;
        }

        return $digits;
    }
}
