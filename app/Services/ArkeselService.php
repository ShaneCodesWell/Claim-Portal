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
        $this->apiKey   = (string) config('services.arkesel.api_key');
        $this->senderId = (string) config('services.arkesel.sender_id');
    }

    public function sendSms(string $phone, string $message): bool
    {
        // Local, or the sandbox "log" driver: log instead of sending
        if (app()->environment('local') || config('services.arkesel.driver') === 'log') {
            Log::info('Arkesel: SMS not sent (log mode)', [
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
