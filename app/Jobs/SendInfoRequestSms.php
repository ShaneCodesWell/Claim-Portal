<?php

namespace App\Jobs;

use App\Models\ClaimInfoRequest;
use App\Services\ArkeselService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class SendInfoRequestSms implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // No automatic retries: a failure is recorded on the request so staff can see it.
    public int $tries = 1;

    public function __construct(public ClaimInfoRequest $infoRequest) {}

    public function handle(ArkeselService $sms): void
    {
        $claim = $this->infoRequest->claim()->with('customer')->first();
        $phone = $this->normalisePhone($claim->customer?->phone);

        $context = [
            'claim_id'        => $claim->id,
            'info_request_id' => $this->infoRequest->id,
            'phone'           => $phone ? $this->mask($phone) : null,
        ];

        if (! $phone) {
            Log::warning('InfoRequest SMS: no usable phone number', $context);
            $this->markFailed('No usable phone number on file for the customer.');
            return;
        }

        // Safety net for notes saved before the cap existed
        $note    = Str::limit($this->smsSafe($this->infoRequest->note), ClaimInfoRequest::NOTE_MAX, '...');
        $message = ClaimInfoRequest::smsMessage($claim, $note);

        try {
            $ok = $sms->sendSms($phone, $message);
        } catch (Throwable $e) {
            Log::error('InfoRequest SMS: exception', $context + ['error' => $e->getMessage()]);
            $this->markFailed($e->getMessage());
            return;
        }

        if ($ok) {
            Log::info('InfoRequest SMS: sent', $context);
            $this->infoRequest->update([
                'sms_status'  => ClaimInfoRequest::SMS_SENT,
                'sms_sent_at' => now(),
                'sms_error'   => null,
            ]);
        } else {
            Log::error('InfoRequest SMS: provider did not accept the message', $context);
            $this->markFailed('The SMS provider did not accept the message.');
        }
    }

    public function failed(Throwable $e): void
    {
        $this->markFailed($e->getMessage());
    }

    private function markFailed(string $reason): void
    {
        $this->infoRequest->update([
            'sms_status' => ClaimInfoRequest::SMS_FAILED,
            'sms_error'  => $reason,
        ]);
    }

    /** 0XXXXXXXXX, 233XXXXXXXXX or +233XXXXXXXXX -> 233XXXXXXXXX */
    private function normalisePhone(?string $raw): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $raw);

        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return $digits;
        }
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return '233' . substr($digits, 1);
        }

        return null;
    }

    private function mask(string $phone): string
    {
        return substr($phone, 0, 5) . '****' . substr($phone, -3);
    }

    /** Flatten whitespace and swap "smart" punctuation for plain characters. */
    private function smsSafe(?string $text): string
    {
        $text = strtr((string) $text, [
            "\u{2018}" => "'",
            "\u{2019}" => "'",
            "\u{201C}" => '"',
            "\u{201D}" => '"',
            "\u{2013}" => '-',
            "\u{2014}" => '-',
            "\u{2026}" => '...',
            "\u{00A0}" => ' ',
        ]);

        return trim(preg_replace('/\s+/u', ' ', $text));
    }
}
