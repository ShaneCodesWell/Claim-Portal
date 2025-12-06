<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class GenovaApiService
{
    private $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.genova.base_url');
    }

    // NEW → Customer Login (phone/email/code)
    public function customerLogin($username)
    {
        return Http::withBasicAuth('appuser', 'XGW1120018730')
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/customer-login', [
                'username' => $username,
            ]);
    }

    // Send OTP
    public function requestOtp($email, $userId)
    {
        return Http::withBasicAuth('appuser', 'XGW1120018730')
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/request-2fa', [
                'email'   => $email,
                'user_id' => $userId,
            ]);
    }

    // Verify OTP
    public function verifyOtp($userId, $otp)
    {
        return Http::withBasicAuth('appuser', 'XGW1120018730')
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/verify-2fa', [
                'user_id' => $userId,
                'otp'     => $otp,
            ]);
    }

    // Fetch Customer Policies
    public function getPolicies($customerCode)
    {
        return Http::withBasicAuth('appuser', 'XGW1120018730')
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/customer-policy', [
                'customer_code' => $customerCode,
            ]);
    }
}
