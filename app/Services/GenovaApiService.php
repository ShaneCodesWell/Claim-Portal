<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use \Illuminate\Http\Client\Response;

class GenovaApiService
{
    private $baseUrl;
    private $username;
    private $password;

    public function __construct()
    {
        $this->baseUrl  = config('services.genova.base_url');
        $this->username = config('services.genova.username');
        $this->password = config('services.genova.password');
    }

    private function client()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->withOptions([
                'verify' => false, // TEMPORARY – SSL chain issue on Genova side
            ])
            ->timeout(30)
            ->asForm();
    }

    /**
     * Verify a customer exists by reusing customer-search.
     * Replaces the old request-claim-otp call — no OTP sent.
     */
    public function customerVerification(string $identifier, string $loginType): Response
    {
        $params = $this->buildSearchParams($identifier, $loginType);

        Log::info('Calling customer-search (verification) with params:', $params);

        return $this->client() // 8 second timeout — fail fast, let local fallback take over
            ->post($this->baseUrl . '/cia/api/mobile/customer-search', $params);
    }

    /**
     * Fetch full policy details including risks/vehicle data.
     * Use policy_id from customer-search results.
     */
    public function policySearch(string $policyId): Response
    {
        Log::info('Calling policy-search', ['policy_id' => $policyId]);

        return $this->clientWithTimeout(60)
            ->post($this->baseUrl . '/cia/api/mobile/policy-search', [
                'policy_id' => $policyId,
            ]);
    }

    // Fetch Business Classes
    public function getBusinessClasses($phoneNumber)
    {
        Log::info('Calling business-class with phone:', ['phone_number' => $phoneNumber]);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/business-class', [
            'phone_number' => $phoneNumber,
        ]);

    }

    // Fetch Products by Business Class
    public function getProductsByClass($businessClassId)
    {
        Log::info('Calling products-by-class with:', ['business_class_id' => $businessClassId]);
        return $this->client()->post($this->baseUrl . '/cia/api/mobile/products-by-class', [
            'business_class_id' => $businessClassId,
        ]);

    }

    private function clientWithTimeout(int $seconds = 30)
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->withOptions(['verify' => false])
            ->timeout($seconds)
            ->asForm();
    }

    private function buildSearchParams(string $identifier, string $loginType): array
    {
        return match ($loginType) {
            'customer_code', 'client_code' => ['client_code' => $identifier],
            'customer_id'    => ['customer_id' => $identifier],
            'policy_number'  => ['policy_number' => $identifier],
            'vehicle_number' => ['vehicle_number' => $identifier],
            'email'          => ['ins_email' => $identifier],
            default          => ['phone_number' => $identifier],
        };
    }

    public function getPolicies($identifier, $type = 'phone_number')
    {
        $params = $this->buildSearchParams($identifier, $type);

        Log::info('Calling customer-search with params:', $params);

        return $this->clientWithTimeout(60) // long timeout fine here — runs in background job
            ->post($this->baseUrl . '/cia/api/mobile/customer-search', $params);
    }
}
