<?php

return [
    'customer_token_minutes' => (int) env('API_CUSTOMER_TOKEN_MINUTES', 60),
    'staff_token_minutes'    => (int) env('API_STAFF_TOKEN_MINUTES', 480),

    'report_window_days' => env('CLAIM_REPORT_WINDOW_DAYS') === null ? null : (int) env('CLAIM_REPORT_WINDOW_DAYS'),

    // Mirrors the web staffLogin() list. One place for the API.
    'staff_roles' => ['admin', 'staff', 'surveyor', 'claims_adjuster'],

    'sandbox_otp'    => env('API_SANDBOX_OTP'),
    'sandbox_phones' => array_values(array_filter(array_map('trim', explode(',', (string) env('API_SANDBOX_PHONES', ''))))),
];