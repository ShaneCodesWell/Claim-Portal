<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AgentSyncService;
use App\Models\Agent;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AgentAuthController extends Controller
{
    public function __construct(protected OtpService $otp) {}

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

        // Throttle: 5 attempts per IP per minute. Every attempt counts, whether or not the
        // number exists, so the limit cannot be reset by trying a valid number in between.
        $throttleKey = 'agent-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => 'error',
                'message' => "Too many attempts. Please try again in {$seconds} seconds.",
            ], 429);
        }

        RateLimiter::hit($throttleKey, 60);

        // Forget anything left over from an earlier attempt in this browser session
        session()->forget(['agent_pending_id', 'agent_pending_name', 'agent_pending_profiles', 'agent_otp_verified']);

        $agents = Agent::where('phone', $phone)
            ->orderBy('name')
            ->get(['id', 'name', 'glims_agent_code', 'genova_agent_code']);

        if ($agents->isEmpty()) {
            Log::warning('AgentAuthController@loginAjax: phone not found', ['phone' => $phone]);

            // Intentionally vague — don't reveal whether the number exists
            return response()->json([
                'status'  => 'error',
                'message' => 'We could not verify your details. Please try again or contact support.',
            ], 422);
        }

        // At most 5 codes per phone number per 10 minutes (stops text-message flooding)
        $sendKey = 'agent-otp-send:' . $phone;
        if (RateLimiter::tooManyAttempts($sendKey, 5)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many codes requested for this number. Please try again in a few minutes.',
            ], 429);
        }

        RateLimiter::hit($sendKey, 600);

        session([
            'agent_pending_auth'  => true,
            'agent_pending_phone' => $phone,
        ]);

        if ($agents->count() === 1) {
            $agent = $agents->first();

            session([
                'agent_pending_id'   => $agent->id,
                'agent_pending_name' => $agent->name,
            ]);
        } else {
            // Several agents share this phone. The server remembers them; the browser gets
            // names only after the code is verified (see verifyOtp).
            session([
                'agent_pending_profiles' => $agents->map(fn($agent) => [
                    'id'   => $agent->id,
                    'name' => $agent->name,
                    'code' => $agent->genova_agent_code ?: $agent->glims_agent_code,
                ])->values()->all(),
            ]);
        }

        // No name is sent before the code is verified
        return $this->sendOtpAndRespond($phone, '');
    }

    // STEP 2: Verify OTP → log in (or show the picker when several agents share the phone)
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

        // Rate limit: 5 wrong codes per phone per 10 minutes, whatever the IP address
        $phoneKey = 'agent-otp-verify-phone:' . $phone;
        if (RateLimiter::tooManyAttempts($phoneKey, 5)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Too many wrong codes. Please try again in a few minutes.',
            ], 429);
        }

        if (! $this->otp->verify($phone, $request->otp)) {
            RateLimiter::hit($throttleKey, 60);
            RateLimiter::hit($phoneKey, 600);
            return response()->json([
                'status'  => 'error',
                'message' => 'Invalid or expired code. Please try again.',
            ], 422);
        }

        RateLimiter::clear($throttleKey);
        RateLimiter::clear($phoneKey);

        // Several agents share this phone and none has been chosen yet:
        // the code is now proven, so show the names (and agent codes) and let them choose.
        if (session('agent_pending_profiles') && ! session('agent_pending_id')) {
            session(['agent_otp_verified' => true]);

            return response()->json([
                'status'   => 'profile_selection',
                'profiles' => collect(session('agent_pending_profiles'))
                    ->map(fn($profile, $index) => [
                        'id'   => $index,
                        'name' => $profile['name'] ?? 'Unknown',
                        'code' => $profile['code'] ?? '',
                    ])
                    ->values(),
            ]);
        }

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

    // Agent chosen. Only possible AFTER the code was verified, and only among the
    // agents the server itself found for this phone (chosen by number, never by agent id).
    public function selectProfile(Request $request): JsonResponse
    {
        if (! session('agent_pending_auth') || ! session('agent_otp_verified')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.'], 401);
        }

        $request->validate(['profile_id' => 'required|integer|min:0']);

        $profiles = session('agent_pending_profiles', []);
        $profile  = $profiles[(int) $request->input('profile_id')] ?? null;

        $agent = $profile ? Agent::find($profile['id']) : null;

        if (! $agent || $agent->phone !== session('agent_pending_phone')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid selection.'], 422);
        }

        $this->completeLogin($agent);

        return response()->json([
            'status'   => 'success',
            'redirect' => route('agent.dashboard.index'),
        ]);
    }

    // Resend OTP
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
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('agent.login')->with('success', 'Logged out successfully.');
    }

    // Private helpers
    private function completeLogin(Agent $agent): void
    {
        Auth::guard('agent')->login($agent);

        // New session id after login (prevents session fixation)
        session()->regenerate();

        session()->forget([
            'agent_pending_auth',
            'agent_pending_id',
            'agent_pending_phone',
            'agent_pending_name',
            'agent_pending_profiles',
            'agent_otp_verified',
        ]);

        app(AgentSyncService::class)->dispatchPolicySync($agent);
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
            'name'         => '',   // never reveal a name before the code is verified
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
