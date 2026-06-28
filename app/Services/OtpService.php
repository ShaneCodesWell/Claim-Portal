<?php
namespace App\Services;

use App\Models\Otp;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OtpService
{
    public function __construct(private ArkeselService $sms)
    {}

    public function send(string $phoneLocal): bool
    {
        // Convert 0XXXXXXXXX → 233XXXXXXXXX for Arkesel
        $phoneIntl = '233' . substr($phoneLocal, 1);

        // Invalidate any existing unused OTPs for this number
        Otp::where('phone', $phoneLocal)->where('used', false)->delete();

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        Otp::create([
            'phone'      => $phoneLocal,
            'code'       => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
        ]);

        // Skip the actual SMS in local — OTP is in the DB for manual lookup
        // if (app()->environment('local')) {
        //     Log::info("OTP [{$code}] generated for {$phoneLocal} — SMS skipped (local)");
        //     return true;
        // }

        $message = "Your Claim Portal verification code is: {$code}. Valid for 10 minutes. Do not share this code with anyone.";

        return $this->sms->sendSms($phoneIntl, $message);
    }

    public function verify(string $phoneLocal, string $code): bool
    {
        $otp = Otp::where('phone', $phoneLocal)
            ->where('code', $code)
            ->where('used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (! $otp) {
            Log::warning('OTP: verification failed', ['phone' => $phoneLocal]);
            return false;
        }

        $otp->update(['used' => true]);

        Log::info('OTP: verified successfully', ['phone' => $phoneLocal]);
        return true;
    }
}
