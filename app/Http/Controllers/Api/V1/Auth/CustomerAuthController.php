<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CustomerResource;
use App\Jobs\SyncCustomerPoliciesJob;
use App\Services\CustomerIdentityService;
use App\Services\OtpService;
use App\Support\ApiResponse;
use App\Support\Sandbox;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Dedoc\Scramble\Attributes\Group;

#[Group('Authentication', weight: 1)]
class CustomerAuthController extends Controller
{
    private const TTL = 600; // challenge lifetime, seconds

    public function __construct(
        private CustomerIdentityService $identity,
        private OtpService $otp,
    ) {}

    /**
     * Request a one-time code
     *
     * @unauthenticated
     */
    public function requestOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'identifier' => ['required', 'string', 'max:100'],
            'login_type' => ['sometimes', 'in:mobile_no,policy_number,vehicle_number'],
        ]);

        $found = $this->identity->lookup($data['identifier'], $data['login_type'] ?? 'mobile_no');

        // In sandbox mode a phone that isn't allowlisted looks exactly like an unknown one
        if (! $found || (Sandbox::active() && ! Sandbox::allowsPhone($found['phone']))) {
            return ApiResponse::error('identity_not_found', 'We could not verify your identity.', 422);
        }

        $phone   = $found['phone'];
        $sendKey = 'api-otp-send:' . $phone;

        if (RateLimiter::tooManyAttempts($sendKey, 5)) {
            return ApiResponse::error('too_many_requests', 'Too many codes requested for this number. Try again in a few minutes.', 429);
        }

        RateLimiter::hit($sendKey, 600);

        if (! $this->otp->send($phone)) {
            Log::error('API requestOtp: OTP send failed', ['phone' => $this->identity->maskPhone($phone)]);
            return ApiResponse::error('otp_send_failed', 'We could not send the verification code. Please try again.', 502);
        }

        $challengeId = Str::random(40);

        Cache::put($this->key($challengeId), [
            'phone'    => $phone,
            'profiles' => $found['profiles'],
            'verified' => false,
        ], self::TTL);

        return response()->json(['data' => [
            'challenge_id' => $challengeId,
            'phone_masked' => $this->identity->maskPhone($phone),
            'expires_in'   => self::TTL,
        ]]);
    }

    /**
     * Verify the one-time code
     *
     * @unauthenticated
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $data = $request->validate([
            'challenge_id' => ['required', 'string', 'size:40'],
            'otp'          => ['required', 'digits:6'],
        ]);

        $challenge = Cache::get($this->key($data['challenge_id']));

        if (! $challenge) {
            return $this->expired();
        }

        $phone      = $challenge['phone'];
        $attemptKey = 'api-otp-verify:' . $data['challenge_id'];
        $phoneKey   = 'api-otp-verify-phone:' . $phone;

        if (RateLimiter::tooManyAttempts($attemptKey, 5) || RateLimiter::tooManyAttempts($phoneKey, 5)) {
            Cache::forget($this->key($data['challenge_id']));
            return ApiResponse::error('too_many_attempts', 'Too many wrong codes. Request a new code.', 429);
        }

        $valid = Sandbox::acceptsOtp($phone, $data['otp']) || $this->otp->verify($phone, $data['otp']);

        if (! $valid) {
            RateLimiter::hit($attemptKey, 600);
            RateLimiter::hit($phoneKey, 600);
            return ApiResponse::error('invalid_otp', 'Invalid or expired code.', 422);
        }

        RateLimiter::clear($attemptKey);
        RateLimiter::clear($phoneKey);

        if (count($challenge['profiles']) === 1) {
            return $this->issueToken($data['challenge_id'], $challenge, 0);
        }

        // Several profiles share this phone. The OTP is proven, so show names (only now) and let them choose.
        $challenge['verified'] = true;
        Cache::put($this->key($data['challenge_id']), $challenge, self::TTL);

        return response()->json(['data' => [
            'status'       => 'profile_selection',
            'challenge_id' => $data['challenge_id'],
            'profiles'     => collect($challenge['profiles'])
                ->map(fn($p, $i) => ['id' => $i, 'name' => $p['name']])
                ->values(),
        ]]);
    }

    /**
     * Choose a profile
     *
     * @unauthenticated
     */
    public function selectProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'challenge_id' => ['required', 'string', 'size:40'],
            'profile_id'   => ['required', 'integer', 'min:0'],
        ]);

        $challenge = Cache::get($this->key($data['challenge_id']));

        if (! $challenge) {
            return $this->expired();
        }

        if (empty($challenge['verified'])) {
            return ApiResponse::error('otp_required', 'Verify the code first.', 403);
        }

        if (! isset($challenge['profiles'][$data['profile_id']])) {
            return ApiResponse::error('invalid_profile', 'Invalid selection.', 422);
        }

        return $this->issueToken($data['challenge_id'], $challenge, $data['profile_id']);
    }

    private function issueToken(string $challengeId, array $challenge, int $index): JsonResponse
    {
        $profile  = $challenge['profiles'][$index];
        $customer = $this->identity->resolveCustomer($challenge['phone'], $profile);

        if (! $customer) {
            return ApiResponse::error('customer_not_found', 'We could not locate your account. Please contact support.', 404);
        }

        Cache::forget($this->key($challengeId)); // single use

        // Pull policies in the background. Never let this block a login.
        try {
            SyncCustomerPoliciesJob::dispatch($customer, $profile['secondary_code'] ?? null)->onQueue('high');
        } catch (\Throwable $e) {
            Log::error('API issueToken: sync dispatch failed', ['customer_id' => $customer->id, 'error' => $e->getMessage()]);
        }

        $expires = now()->addMinutes(config('api.customer_token_minutes'));
        $token   = $customer->createToken('api-customer', ['customer'], $expires);

        return response()->json(['data' => [
            'status'       => 'authenticated',
            'token_type'   => 'Bearer',
            'access_token' => $token->plainTextToken,
            'expires_at'   => $expires->toIso8601String(),
            'customer'     => new CustomerResource($customer),
        ]]);
    }

    private function key(string $challengeId): string
    {
        return 'api-challenge:' . $challengeId;
    }

    private function expired(): JsonResponse
    {
        return ApiResponse::error('challenge_expired', 'This verification has expired. Start again.', 422);
    }
}
