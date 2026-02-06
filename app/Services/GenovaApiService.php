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

    private function client()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->withOptions([
                'verify' => false,   // TEMPORARY – SSL chain issue on Genova side
            ])
            ->timeout(30)
            ->asForm();
    }

    // Step 1: Customer Verification - sends OTP immediately
    public function customerVerification($identifier, $type = 'mobile_no')
    {
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

        // return Http::withBasicAuth($this->username, $this->password)
        //     ->timeout(30)
        //     ->asForm()
        //     ->post($this->baseUrl . '/cia/api/mobile/request-claim-otp', $params);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/request-claim-otp', $params);
    }

    // Step 2: Verify Claim OTP (THIS IS THE CORRECT ENDPOINT)
    public function verifyClaimOtp($userId, $twoFaCode)
    {
        Log::info('Calling verify-claim-otp with params:', [
            'user_id' => $userId,
            'two_fa_code' => $twoFaCode
        ]);

        // return Http::withBasicAuth($this->username, $this->password)
        //     ->timeout(30)
        //     ->asForm()
        //     ->post($this->baseUrl . '/cia/api/mobile/verify-claim-otp', [
        //         'user_id' => $userId,
        //         'two_fa_code' => $twoFaCode,
        //     ]);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/verify-claim-otp', [
            'user_id' => $userId,
            'two_fa_code' => $twoFaCode,
        ]);

    }

    // Fetch Business Classes
    public function getBusinessClasses($phoneNumber)
    {
        Log::info('Calling business-class with phone:', ['phone_number' => $phoneNumber]);

        // return Http::withBasicAuth($this->username, $this->password)
        //     ->timeout(30)
        //     ->asForm()
        //     ->post($this->baseUrl . '/cia/api/mobile/business-class', [
        //         'phone_number' => $phoneNumber,
        //     ]);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/business-class', [
            'phone_number' => $phoneNumber,
        ]);

    }

    // Fetch Products by Business Class
    public function getProductsByClass($businessClassId)
    {
        Log::info('Calling products-by-class with:', ['business_class_id' => $businessClassId]);

        // return Http::withBasicAuth($this->username, $this->password)
        //     ->timeout(30)
        //     ->asForm()
        //     ->post($this->baseUrl . '/cia/api/mobile/products-by-class', [
        //         'business_class_id' => $businessClassId,
        //     ]);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/products-by-class', [
            'business_class_id' => $businessClassId,
        ]);

    }

    // Fetch Customer Policies
    // public function getPolicies($customerCodeOrPhone)
    // {
    //     return Http::withBasicAuth($this->username, $this->password)
    //         ->timeout(30)
    //         ->asForm()
    //         ->post($this->baseUrl . '/cia/api/mobile/customer-search', [
    //             'phone_number' => $customerCodeOrPhone,
    //         ]);
    // }

    public function getPolicies($identifier, $type = 'phone_number')
    {
        $params = [];

        switch ($type) {
            case 'customer_code':
            case 'client_code':
                $params['client_code'] = $identifier;
                break;
            case 'customer_id':
                $params['customer_id'] = $identifier;
                break;
            case 'email':
                $params['ins_email'] = $identifier;
                break;
            case 'phone_number':
            default:
                $params['phone_number'] = $identifier;
                break;
        }

        Log::info('Calling customer-search with params:', $params);

        // return Http::withBasicAuth($this->username, $this->password)
        //     ->timeout(30)
        //     ->asForm()
        //     ->post($this->baseUrl . '/cia/api/mobile/customer-search', $params);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/customer-search', $params);

    }
}
