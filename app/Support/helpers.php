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

if (! function_exists('formatDateTime')) {
    function formatDateTime($date, string $format = 'F j, Y g:i A'): string
    {
        return formatDate($date, $format);
    }
}
