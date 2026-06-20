<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SyncAgentPoliciesJob;
use App\Models\Agent;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AgentAuthController extends Controller
{
    public function __construct(protected OtpService $otp)
    {}

    // Views

    public function showLoginForm()
    {
        return view('auth.agent-login');
    }

    // STEP 1: Verify phone → send OTP
    public function loginAjax(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        $phone = $this->normalizeGhanaPhone(
            preg_replace('/[\x{00A0}\x{FEFF}]+/u', '', trim($request->phone))
        );

        // Throttle: 5 attempts per IP per minute
        $throttleKey = 'agent-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => 'error',
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        $agent = Agent::where('phone', $phone)->first();

        if (! $agent) {
            RateLimiter::hit($throttleKey, 60);

            Log::warning('AgentAuthController@loginAjax: phone not found', ['phone' => $phone]);

            // Intentionally vague — don't reveal whether the number exists
            return response()->json([
                'status'  => 'error',
                'message' => 'We could not verify your details. Please try again or contact support.',
            ], 422);
        }

        RateLimiter::clear($throttleKey);

        session([
            'agent_pending_auth'  => true,
            'agent_pending_id'    => $agent->id,
            'agent_pending_phone' => $phone,
            'agent_pending_name'  => $agent->name,
        ]);

        return $this->sendOtpAndRespond($phone, $agent->name);
    }

    // STEP 2: Verify OTP → log in
    public function verifyOtp(Request $request): JsonResponse
    {
        if (! session('agent_pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.'], 401);
        }

        $request->validate(['otp' => 'required|digits:6']);

        $phone = session('agent_pending_phone');
        if (! $phone) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.'], 401);
        }

        // Rate limit: 5 OTP attempts per IP per minute
        $throttleKey = 'agent-otp-verify:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => 'error',
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        if (! $this->otp->verify($phone, $request->otp)) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid or expired code. Please try again.',
            ], 422);
        }

        RateLimiter::clear($throttleKey);

        $agent = Agent::find(session('agent_pending_id'));

        if (! $agent) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Agent account not found. Please contact support.',
            ], 404);
        }

        $this->completeLogin($agent);

        return response()->json([
            'status'   => 'success',
            'redirect' => route('agent.dashboard.index'),
        ]);
    }

    // STEP 2b: Resend OTP
    public function resendOtp(Request $request): JsonResponse
    {
        if (! session('agent_pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired.'], 401);
        }

        $phone = session('agent_pending_phone');
        if (! $phone) {
            return response()->json(['status' => 'error', 'message' => 'No phone number in session.'], 422);
        }

        // Max 3 resends per phone per 10 minutes
        $throttleKey = 'agent-otp-resend:' . $phone;
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => 'error',
                'message' => "Too many resend attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        RateLimiter::hit($throttleKey, 600);

        if (! $this->otp->send($phone)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to resend code. Please try again.',
            ], 500);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'A new code has been sent to your phone.',
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('agent')->logout();
        $request->session()->flush();

        return redirect()->route('agent.login')->with('success', 'Logged out successfully.');
    }

    // Private helpers
    private function completeLogin(Agent $agent): void
    {
        Auth::guard('agent')->login($agent);

        session()->forget([
            'agent_pending_auth',
            'agent_pending_id',
            'agent_pending_phone',
            'agent_pending_name',
        ]);

        try {
            SyncAgentPoliciesJob::dispatch($agent);
        } catch (\Exception $e) {
            // Never let a sync failure block the login
            Log::error('AgentAuthController: sync job dispatch failed', [
                'agent_id' => $agent->id,
                'error'    => $e->getMessage(),
            ]);
        }
    }

    private function sendOtpAndRespond(string $phone, string $name): JsonResponse
    {
        if (! $this->otp->send($phone)) {
            Log::error('AgentAuthController: Failed to send OTP', ['phone' => $phone]);
            return response()->json([
                'status'  => 'error',
                'message' => 'We found your account but could not send the verification code. Please try again.',
            ], 500);
        }

        return response()->json([
            'status'       => 'otp_sent',
            'name'         => $name,
            'phone_masked' => $this->maskPhone($phone),
        ]);
    }

    private function maskPhone(string $phone): string
    {
        if (strlen($phone) >= 7) {
            return substr($phone, 0, 3) . '***' . substr($phone, -4);
        }
        return '***';
    }

    private function normalizeGhanaPhone(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return '0' . substr($digits, 3);
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return $digits;
        }

        if (strlen($digits) === 9) {
            return '0' . $digits;
        }

        return $digits;
    }
}
