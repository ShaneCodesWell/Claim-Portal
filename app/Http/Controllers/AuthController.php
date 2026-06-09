<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\GlimsService;
use App\Services\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    protected $api;
    protected $glims;
    protected OtpService $otp;

    public function __construct(GenovaApiService $api, GlimsService $glims, OtpService $otp)
    {
        $this->api   = $api;
        $this->glims = $glims;
        $this->otp   = $otp;
    }

    // STEP 1 → Login Screen
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function showUserSelectForm()
    {
        return view('auth.user-select');
    }

    public function staffLoginForm()
    {
        return view('auth.staff-login');
    }

    public function agentLoginForm()
    {
        return view('auth.agent-login');
    }

    // Agent login method
    public function agentLogin(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'phone'    => $request->phone,
            'password' => $request->password,
        ];

        if (! Auth::guard('agent')->attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['phone' => 'Invalid phone number or password.'])
                ->withInput($request->only('phone'));
        }

        $request->session()->regenerate();

        return redirect()->intended(route('agent.dashboard.index'));
    }

    public function staffLogin(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            // Make sure it's actually a staff/admin user
            if (! $user->is_admin && $user->role === null) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'You do not have staff access.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // Redirect based on role
            return match ($user->role) {
                'Admin' => redirect()->route('staff.claims.index'),
                default => redirect()->route('staff.claims.index'),
            };
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    // New Ajax Login
    // ── STEP 1: Verify phone exists, resolve profiles ─────────────────
    public function loginAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'username'   => 'required|string',
            'login_type' => 'sometimes|in:mobile_no,policy_number,vehicle_number',
        ]);

        $identifier = preg_replace('/[\x{00A0}\x{FEFF}]+/u', '', trim($request->username));

        if ($request->input('login_type', 'mobile_no') === 'mobile_no') {
            $identifier = $this->normalizeGhanaPhone($identifier);
        }

        $loginType = $request->input('login_type', 'mobile_no');

        // ── STEP 1: Verify with Genova ────────────────────────────────
        $genovaResult = $this->attemptGenovaVerification($identifier, $loginType);

        if ($genovaResult['success']) {
            $data        = $genovaResult['data'];
            $userId      = $data['user_id'];
            $phoneNumber = $data['search_used']['phone_no'] ?? ($loginType === 'mobile_no' ? $identifier : null);

            session([
                'pending_auth'    => true,
                'pending_user_id' => $userId,
                'pending_phone'   => $phoneNumber,
                'pending_name'    => $data['name'],
                'login_type'      => $loginType,
                'username'        => $identifier,
            ]);

            $profiles = $this->resolveProfiles($phoneNumber, $userId);

            if (empty($profiles)) {
                return response()->json([
                    'status'  => 'no_profile',
                    'message' => 'Your phone number was verified but we could not find an associated account. Please contact support.',
                ]);
            }

            if (count($profiles) === 1) {
                session(['pending_customer_code' => $profiles[0]['code']]);
                return $this->sendOtpAndRespond($phoneNumber, $data['name']);
            }

            return response()->json([
                'status'   => 'profile_selection',
                'profiles' => $profiles,
                'message'  => 'Multiple profiles found. Please select yours.',
            ]);
        }

        // ── Genova failed — try local Genova DB ───────────────────────
        Log::warning('loginAjax: Genova API failed, trying local Genova DB', [
            'identifier' => $identifier,
            'reason'     => $genovaResult['reason'],
        ]);

        $genovaLocalProfiles = $this->resolveGenovaLocalProfiles($identifier, $loginType);

        if (! empty($genovaLocalProfiles)) {
            $profile = $genovaLocalProfiles[0];

            session([
                'pending_auth'          => true,
                'pending_phone'         => $profile['phone'],
                'pending_name'          => $profile['name'],
                'pending_customer_code' => $profile['code'],
                'selected_customer_id'  => Customer::where('external_customer_code', $profile['code'])->value('id'),
                'login_type'            => $loginType,
                'auth_source'           => 'genova_local',
            ]);

            if (count($genovaLocalProfiles) > 1) {
                return response()->json([
                    'status'   => 'profile_selection',
                    'profiles' => $genovaLocalProfiles,
                    'source'   => 'genova_local',
                ]);
            }

            return $this->sendOtpAndRespond($profile['phone'], $profile['name']);
        }

        // ── Local Genova empty — try GLIMS ────────────────────────────
        Log::info('loginAjax: local Genova lookup empty, trying GLIMS', ['identifier' => $identifier]);

        if ($loginType === 'mobile_no') {
            $glimsProfiles = $this->resolveGlimsProfiles($identifier);

            if (! empty($glimsProfiles)) {
                $profile = $glimsProfiles[0];
                $phone   = $profile['phone'] ?? $identifier;

                session([
                    'pending_auth'          => true,
                    'pending_phone'         => $phone,
                    'pending_name'          => $profile['name'],
                    'pending_customer_code' => $profile['code'],
                    'selected_customer_id'  => Customer::where('external_customer_code', $profile['code'])->value('id'),
                    'login_type'            => $loginType,
                    'auth_source'           => 'glims',
                ]);

                if (count($glimsProfiles) > 1) {
                    return response()->json([
                        'status'   => 'profile_selection',
                        'profiles' => $glimsProfiles,
                        'source'   => 'glims',
                    ]);
                }

                return $this->sendOtpAndRespond($phone, $profile['name']);
            }
        }

        // ── Nothing found anywhere ────────────────────────────────────
        Log::error('loginAjax: All auth sources failed', [
            'identifier' => $identifier,
            'login_type' => $loginType,
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'We could not verify your identity. Please try again or contact support.',
        ], 422);
    }

    // ── STEP 2: Profile selected → check password status ─────────────
    public function selectProfile(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session('pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired.'], 401);
        }

        $request->validate(['customer_code' => 'required|string']);

        $customerCode = $request->customer_code;
        session(['pending_customer_code' => $customerCode]);

        // Pin the local DB record if it exists
        $customer = Customer::where('external_customer_code', $customerCode)->first();
        if ($customer) {
            session(['selected_customer_id' => $customer->id]);

            // If phone is missing from session but exists on local record, use it
            if (! session('pending_phone') && $customer->phone) {
                session(['pending_phone' => $customer->phone]);
            }
        }

        $phone = session('pending_phone');

        if (! $phone) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No phone number found for this profile. Please contact support.',
            ], 422);
        }

        return $this->sendOtpAndRespond($phone, session('pending_name', 'there'));
    }

    public function verifyOtpAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session('pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.'], 401);
        }

        $request->validate(['otp' => 'required|digits:6']);

        $phone = session('pending_phone');
        if (! $phone) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.'], 401);
        }

        // Rate limit: 5 attempts per IP per minute
        $throttleKey = 'otp-verify:' . $request->ip();
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

        $customer = $this->resolveCustomerFromSession($phone);

        if (! $customer) {
            return response()->json([
                'status'  => 'error',
                'message' => 'We could not locate your account. Please contact support.',
            ], 404);
        }

        $this->completeLogin($customer);

        return response()->json([
            'status'   => 'success',
            'redirect' => route('dashboard'),
        ]);
    }

    public function resendOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session('pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired.'], 401);
        }

        $phone = session('pending_phone');
        if (! $phone) {
            return response()->json(['status' => 'error', 'message' => 'No phone number in session.'], 422);
        }

        // Max 3 resends per phone per 10 minutes
        $throttleKey = 'otp-resend:' . $phone;
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

    // ── PRIVATE: Complete login — set full session ────────────────────
    private function completeLogin(Customer $customer): void
    {
        Auth::guard('customer')->login($customer);

        // Clean up all pending pre-auth session data
        session()->forget([
            'pending_auth',
            'pending_user_id',
            'pending_phone',
            'pending_name',
            'pending_customer_code',
            'selected_customer_id',
            'auth_source',
            'login_type',
            'username',
        ]);
    }

    // ── PRIVATE: Resolve profiles from Genova + GLIMS ─────────────────
    private function resolveProfiles(?string $phone, int $userId): array
    {
        $profiles = [];

        if (! $phone) {
            return $profiles;
        }

        try {
            $response = $this->api->getPolicies($phone, 'phone_number');

            if ($response->successful()) {
                $content = $response->json('data.content') ?? [];

                foreach ($content as $entry) {
                    if (empty($entry['code'])) {
                        continue;
                    }

                    $profiles[] = [
                        'code'         => $entry['code'],
                        'name'         => $entry['name'] ?? 'Unknown',
                        'phone'        => $entry['phone_number'] ?? $phone,
                        'email'        => $entry['email'] ?? null,
                        'policy_count' => count($entry['policies'] ?? []),
                        'source'       => 'genova',
                        'is_match'     => (string) ($entry['customer_api_identity'] ?? '') === (string) $userId,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('resolveProfiles: Genova customer-search failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // Also check GLIMS for this phone
        $glimsProfiles = $this->resolveGlimsProfiles($phone);
        foreach ($glimsProfiles as $gp) {
            // Avoid duplicates — if already in Genova profiles, skip
            $alreadyIn = collect($profiles)->pluck('code')->contains($gp['code']);
            if (! $alreadyIn) {
                $profiles[] = $gp;
            }
        }

        return $profiles;
    }

    // ── PRIVATE: Resolve profiles from GLIMS only ─────────────────────
    private function resolveGlimsProfiles(string $phone): array
    {
        $profiles = [];

        // Try Oracle GLIMS first
        if ($this->glims->isConnected()) {
            try {
                $customer = $this->glimsPhoneLookup($phone);

                if ($customer) {
                    $name = trim(implode(' ', array_filter([
                        $customer['CLIENT_FIRST_NAME'] ?? null,
                        $customer['CLIENT_MIDDLE_NAME'] ?? null,
                        $customer['CLIENT_FAMILY_NAME'] ?? null,
                    ])));

                    $profiles[] = [
                        'code'         => $customer['CLIENT_CODE'],
                        'name'         => $name,
                        'phone'        => $customer['CLIENT_HOME_MOBILE'] ?? $customer['CLIENT_HOME_TEL'] ?? $phone,
                        'email'        => $customer['CLIENT_HOME_EMAIL'] ?? null,
                        'policy_count' => null,
                        'source'       => 'glims',
                        'is_match'     => true,
                    ];

                    return $profiles; // Oracle found them — no need for local lookup
                }
            } catch (\Exception $e) {
                Log::warning('resolveGlimsProfiles: Oracle lookup failed', ['error' => $e->getMessage()]);
            }
        }

        // Oracle unreachable or returned nothing — fall back to local synced DB
        Log::info('resolveGlimsProfiles: falling back to local DB', ['phone' => $phone]);

        $localCustomer = Customer::where('phone', $phone)
            ->whereJsonContains('sources', 'glims') // only GLIMS-sourced customers
            ->first();

        if ($localCustomer) {
            $profiles[] = [
                'code'         => $localCustomer->external_customer_code,
                'name'         => $localCustomer->name,
                'phone'        => $localCustomer->phone,
                'email'        => $localCustomer->email,
                'policy_count' => $localCustomer->policies()->count(),
                'source'       => 'glims_local', // flag so we know it came from local
                'is_match'     => true,
            ];
        }

        return $profiles;
    }

    // ── PRIVATE HELPERS ───────────────────────────────────────────
    /**
     * Attempt Genova verification, return a normalised result array.
     */
    private function attemptGenovaVerification(string $identifier, string $loginType): array
    {
        try {
            $response = $this->api->customerVerification($identifier, $loginType);

            if ($response->failed()) {
                return [
                    'success' => false,
                    'reason'  => 'http_' . $response->status(),
                ];
            }

            $data = $response->json('data');

            if (empty($data) || ! isset($data['user_id'])) {
                return ['success' => false, 'reason' => 'empty_response'];
            }

            return ['success' => true, 'data' => $data];

        } catch (\Exception $e) {
            Log::error('attemptGenovaVerification exception: ' . $e->getMessage());
            return ['success' => false, 'reason' => 'exception'];
        }
    }

    /**
     * Resolve profiles from the local DB for customers synced from Genova.
     * Called when the Genova API is unreachable or returns a failure.
     * Mirrors resolveGlimsProfiles() but for source = 'genova'.
     *
     * Supports all three login types:
     *   mobile_no      → match on customers.phone
     *   policy_number  → join through policies table (source = 'genova')
     *   vehicle_number → search raw_payload JSONB for the vehicle number
     */
    private function resolveGenovaLocalProfiles(string $identifier, string $loginType): array
    {
        $profiles = [];

        try {
            $customer = null;

            switch ($loginType) {
                case 'mobile_no':
                case 'phone':
                    $customer = Customer::whereJsonContains('sources', 'genova')
                        ->where('phone', $identifier)
                        ->first();
                    break;

                case 'policy_number':
                    // Find the customer who owns this policy (synced from Genova)
                    $policy = Policy::where('source', 'genova')
                        ->where('policy_number', $identifier)
                        ->with('customer')
                        ->first();

                    $customer = $policy?->customer;
                    break;

                case 'vehicle_number':
                    // Vehicle number is stored inside raw_payload JSON array
                    // PostgreSQL JSONB: search within the array of sub-policy objects
                    $policy = Policy::where('source', 'genova')
                        ->whereRaw(
                            "EXISTS (
                            SELECT 1 FROM jsonb_array_elements(raw_payload::jsonb) elem
                            WHERE elem->>'vehicle_number' = ?
                        )",
                            [$identifier]
                        )
                        ->with('customer')
                        ->first();

                    $customer = $policy?->customer;
                    break;
            }

            if (! $customer) {
                Log::info('resolveGenovaLocalProfiles: no local Genova record found', [
                    'identifier' => $identifier,
                    'login_type' => $loginType,
                ]);
                return [];
            }

            Log::info('resolveGenovaLocalProfiles: found local Genova customer', [
                'customer_id' => $customer->id,
                'phone'       => $customer->phone,
            ]);

            $profiles[] = [
                'code'         => $customer->external_customer_code,
                'name'         => $customer->name,
                'phone'        => $customer->phone,
                'email'        => $customer->email,
                'policy_count' => $customer->policies()->where('source', 'genova')->count(),
                'source'       => 'genova_local', // flag: came from local DB, not live API
                'is_match'     => true,
            ];

        } catch (\Exception $e) {
            Log::error('resolveGenovaLocalProfiles error: ' . $e->getMessage(), [
                'identifier' => $identifier,
                'login_type' => $loginType,
            ]);
        }

        return $profiles;
    }

    /**
     * Phone number lookup against GLIMS GN2_CLIENT table.
     * Returns the same array shape as GlimsService::customerVerification().
     */
    private function glimsPhoneLookup(string $phone): ?array
    {
        try {
            $customer = DB::connection('oracle')
                ->table('GN2_CLIENT')
                ->where('CLIENT_HOME_MOBILE', $phone)
                ->orWhere('CLIENT_HOME_TEL', $phone)
                ->select([
                    'CLIENT_CODE',
                    'CLIENT_FIRST_NAME',
                    'CLIENT_MIDDLE_NAME',
                    'CLIENT_FAMILY_NAME',
                    'CLIENT_HOME_MOBILE',
                    'CLIENT_HOME_TEL',
                    'CLIENT_HOME_EMAIL',
                    'CLIENT_TYPE',
                ])
                ->first();

            if (! $customer) {
                return null;
            }

            return [
                'CLIENT_CODE'        => $customer->client_code,
                'CLIENT_FIRST_NAME'  => $customer->client_first_name,
                'CLIENT_MIDDLE_NAME' => $customer->client_middle_name,
                'CLIENT_FAMILY_NAME' => $customer->client_family_name,
                'CLIENT_HOME_MOBILE' => $customer->client_home_mobile,
                'CLIENT_HOME_TEL'    => $customer->client_home_tel,
                'CLIENT_HOME_EMAIL'  => $customer->client_home_email,
                'CLIENT_TYPE'        => $customer->client_type,
            ];

        } catch (\Exception $e) {
            Log::error('glimsPhoneLookup error: ' . $e->getMessage());
            return null;
        }
    }

    private function sendOtpAndRespond(string $phone, string $name): \Illuminate\Http\JsonResponse
    {
        if (! $this->otp->send($phone)) {
            Log::error('sendOtpAndRespond: Failed to send OTP', ['phone' => $phone]);
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

    private function resolveCustomerFromSession(string $phone): ?Customer
    {
        $customerCode = session('pending_customer_code');
        $pendingName  = session('pending_name');
        $authSource   = session('auth_source', 'genova');

        $customer = null;

        // 1. By customer code (most reliable)
        if ($customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)->first();
        }

        // 2. By phone — catches GLIMS records that have phone stored
        if (! $customer) {
            $customer = Customer::where('phone', $phone)->first();
        }

        if ($customer) {
            $updates = [];

            // Backfill phone if it was null (common in GLIMS-synced records)
            if (empty($customer->phone)) {
                $updates['phone'] = $phone;
            }

            // Backfill customer code if it was missing
            if (empty($customer->external_customer_code) && $customerCode) {
                $updates['external_customer_code'] = $customerCode;
            }

            if (! empty($updates)) {
                $customer->update($updates);
            }

            return $customer->fresh();
        }

        // 3. Create if truly not found — match existing GLIMS record format
        if (! $customerCode) {
            Log::error('resolveCustomerFromSession: no customer code, cannot create', ['phone' => $phone]);
            return null;
        }

        $sources = str_contains($authSource, 'glims') ? ['glims'] : ['genova'];

        Log::info('resolveCustomerFromSession: creating new customer record', [
            'customer_code' => $customerCode,
            'phone'         => $phone,
            'source'        => $authSource,
        ]);

        return Customer::create([
            'external_customer_id'   => null,
            'external_customer_code' => $customerCode,
            'name'                   => $pendingName ?? 'Unknown',
            'phone'                  => $phone,
            'email'                  => null,
            'sources'                => $sources,
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
        // Strip everything that isn't a digit
        $digits = preg_replace('/\D/', '', $phone);

        // Already international: 233XXXXXXXXX (12 digits)
        if (str_starts_with($digits, '233') && strlen($digits) === 12) {
            return '0' . substr($digits, 3); // → 0XXXXXXXXX
        }

        // Local format already correct: 0XXXXXXXXX (10 digits)
        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            return $digits;
        }

        // Bare 9-digit number (e.g. 503845696) — just prepend 0
        if (strlen($digits) === 9) {
            return '0' . $digits;
        }

        // Can't confidently normalize — return cleaned digits and let it fail gracefully
        return $digits;
    }

    // Logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('user.select')->with('success', 'Logged out successfully.');
    }

}
