<?php

namespace App\Support;

class Sandbox
{
    public static function active(): bool
    {
        return (bool) config('app.sandbox');
    }

    public static function allowsPhone(string $phone): bool
    {
        $needle = self::tail($phone);

        foreach (config('api.sandbox_phones', []) as $allowed) {
            if ($needle !== '' && self::tail($allowed) === $needle) {
                return true;
            }
        }

        return false;
    }

    /** True only in sandbox mode, for an allowlisted phone, with the configured code. */
    public static function acceptsOtp(string $phone, string $otp): bool
    {
        $code = (string) config('api.sandbox_otp');

        return self::active()
            && $code !== ''
            && self::allowsPhone($phone)
            && hash_equals($code, $otp);
    }

    // Compare the last 9 digits so 0241234567, 233241234567 and +233 24 123 4567 all match
    private static function tail(string $phone): string
    {
        return substr(preg_replace('/\D/', '', $phone), -9);
    }
}