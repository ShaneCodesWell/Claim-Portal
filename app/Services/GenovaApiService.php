<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Log;

class GenovaApiService
{
    private $baseUrl;
    private $username;
    private $password;

    public function __construct()
    {
        $this->baseUrl = config('services.genova.base_url');
        $this->username = config('services.genova.username');
        $this->password = config('services.genova.password');
    }

    // Step 1: Customer Verification - accepts phone, policy number, or vehicle number
    public function customerVerification($identifier, $type = 'mobile_no')
    {
        // Determine which parameter to send based on type
        $params = [];
        
        switch ($type) {
            case 'mobile_no':
            case 'phone':
                $params['mobile_no'] = $identifier;
                break;
            case 'policy_number':
                $params['policy_number'] = $identifier;
                break;
            case 'vehicle_number':
                $params['vehicle_number'] = $identifier;
                break;
            default:
                $params['mobile_no'] = $identifier;
        }

        Log::info('Calling request-claim-otp with params:', $params);

        return Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/request-claim-otp', $params);
    }

    // Step 2: Request 2FA (sends OTP to email or mobile)
    public function request2FA($email = null, $mobile = null, $userId = null)
    {
        $params = array_filter([
            'email' => $email,
            'mobile' => $mobile,
            'user_id' => $userId,
        ]);

        return Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/request-2fa', $params);
    }

    // Step 3: Verify 2FA (verify the OTP code)
    public function verify2FA($userId, $twoFaCode)
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/verify-2fa', [
                'user_id' => $userId,
                'two_fa_code' => $twoFaCode,
            ]);
    }

    // Fetch Customer Policies
    public function getPolicies($customerCode)
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->timeout(30)
            ->asForm()
            ->post($this->baseUrl . '/cia/api/mobile/customer-policy', [
                'customer_code' => $customerCode,
            ]);
    }
}