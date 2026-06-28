<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ArkeselService
{
    private string $apiKey;
    private string $senderId;
    private string $baseUrl = 'https://sms.nissitechnologies.com';

    public function __construct()
    {
        $this->apiKey   = config('services.arkesel.api_key');
        $this->senderId = config('services.arkesel.sender_id');
    }

    public function sendSms(string $phone, string $message): bool
    {
        // Skip actual send in local — log instead
        if (app()->environment('local')) {
            Log::info('Arkesel: SMS skipped (local)', [
                'phone'   => $phone,
                'message' => $message,
            ]);
            return true;
        }
        
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
            ])->post("{$this->baseUrl}/api/v2/sms/send", [
                'sender'     => $this->senderId,
                'message'    => $message,
                'recipients' => [$phone], // expects 233XXXXXXXXX format
            ]);

            if ($response->successful()) {
                Log::info('Arkesel: SMS sent', ['phone' => $phone]);
                return true;
            }

            Log::error('Arkesel: SMS failed', [
                'phone' => $phone,
                'url'   => "{$this->baseUrl}/api/v2/sms/send",
                'status'   => $response->status(),
                'response' => $response->json(),
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Arkesel: Exception', [
                'phone'   => $phone,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
