<?php

use Carbon\Carbon;

if (! function_exists('formatDate')) {
    function formatDate($date, string $format = 'F j, Y'): string
    {
        if (blank($date)) {
            return '';
        }

        try {
            return Carbon::parse($date)->format($format);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
