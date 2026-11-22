<?php

return [
    'customer_token_minutes' => (int) env('API_CUSTOMER_TOKEN_MINUTES', 60),
    'staff_token_minutes'    => (int) env('API_STAFF_TOKEN_MINUTES', 480),

    // 0 disables the limit
    'report_window_days' => (int) env('CLAIM_REPORT_WINDOW_DAYS', 90),
    'handler_roles'      => ['admin', 'claim_head', 'staff', 'claims_adjuster'],

    // Mirrors the web staffLogin() list. One place for the API.
    'staff_roles' => App\Enums\UserRole::staffRoles(),

    'sandbox_otp'    => env('API_SANDBOX_OTP'),
    'sandbox_phones' => array_values(array_filter(array_map('trim', explode(',', (string) env('API_SANDBOX_PHONES', ''))))),
];
