<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Policy;
use App\Services\GenovaApiService;
use App\Services\GlimsApiService;
use App\Services\OtpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use \App\Jobs\SyncCustomerPoliciesJob;

class AuthController extends Controller
{
    protected $api;
    protected $glims;
    protected OtpService $otp;

    public function __construct(GenovaApiService $api, GlimsApiService $glims, OtpService $otp)
    {
        $this->api   = $api;
        $this->glims = $glims;
        $this->otp   = $otp;
    }

    public function showUserSelectForm()
    {
        return view('auth.user-select');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function staffLoginForm()
    {
        return view('auth.staff-login');
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
                'Admin'    => redirect()->route('staff.claims.index'),
                'surveyor' => redirect()->route('surveyor.claims.index'),
                default    => redirect()->route('staff.claims.index'),
            };
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
    }

    // New Ajax Login
    // ── STEP 1: Verify phone exists, resolve profiles ─────────────────
    public function loginAjax(Request $request): JsonResponse
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

        // STEP 1: Verify with Genova
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

        // Genova failed — try local Genova DB
        Log::warning('loginAjax: Genova API failed, trying local Genova DB', [
            'identifier' => $identifier,
            'reason'     => $genovaResult['reason'],
        ]);

        $genovaLocalProfiles = $this->resolveGenovaLocalProfiles($identifier, $loginType);

        if (! empty($genovaLocalProfiles)) {

            // Only store what is common — not profile-specific data yet
            session([
                'pending_auth'  => true,
                'pending_phone' => $genovaLocalProfiles[0]['phone'],
                'login_type'    => $loginType,
                'auth_source'   => 'genova_local',
            ]);

            if (count($genovaLocalProfiles) > 1) {
                // Multiple profiles — let the user pick; selectProfile() will set the rest
                return response()->json([
                    'status'   => 'profile_selection',
                    'profiles' => $genovaLocalProfiles,
                    'source'   => 'genova_local',
                ]);
            }

            // Single profile — safe to commit everything now
            $profile = $genovaLocalProfiles[0];
            session([
                'pending_name'          => $profile['name'],
                'pending_customer_code' => $profile['code'],
                'selected_customer_id'  => Customer::where('external_customer_code', $profile['code'])->value('id'),
            ]);

            return $this->sendOtpAndRespond($profile['phone'], $profile['name']);
        }

        // Local Genova empty — try GLIMS
        Log::info('loginAjax: local Genova lookup empty, trying GLIMS', ['identifier' => $identifier]);

        if ($loginType === 'mobile_no') {
            $glimsProfiles = $this->resolveGlimsProfiles($identifier);

            if (! empty($glimsProfiles)) {
                $phone = $glimsProfiles[0]['phone'] ?? $identifier;

                // Only store what is common — not profile-specific data yet
                session([
                    'pending_auth'  => true,
                    'pending_phone' => $phone,
                    'login_type'    => $loginType,
                    'auth_source'   => 'glims',
                ]);

                if (count($glimsProfiles) > 1) {
                    // Multiple profiles — let the user pick; selectProfile() will set the rest
                    return response()->json([
                        'status'   => 'profile_selection',
                        'profiles' => $glimsProfiles,
                        'source'   => 'glims',
                    ]);
                }

                // Single profile — safe to commit everything now
                $profile = $glimsProfiles[0];
                session([
                    'pending_name'          => $profile['name'],
                    'pending_customer_code' => $profile['code'],
                    'selected_customer_id'  => Customer::where('external_customer_code', $profile['code'])->value('id'),
                ]);

                return $this->sendOtpAndRespond($phone, $profile['name']);
            }
        }

        // Nothing found anywhere
        Log::error('loginAjax: All auth sources failed', [
            'identifier' => $identifier,
            'login_type' => $loginType,
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'We could not verify your identity. Please try again or contact support.',
        ], 422);
    }

    // STEP 2: Profile selected
    public function selectProfile(Request $request): JsonResponse
    {
        if (! session('pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired.'], 401);
        }

        $request->validate([
            'customer_code'  => 'required|string',
            'name'           => 'sometimes|string',
            'secondary_code' => 'sometimes|nullable|string',
        ]);

        $customerCode = $request->customer_code;
        session(['pending_customer_code' => $customerCode]);

        // ── NEW: persist secondary code for merged cross-system profiles ──
        if ($secondaryCode = $request->input('secondary_code')) {
            session(['pending_secondary_code' => $secondaryCode]);
        }

        // Pin the local DB record if it exists and pull the correct name + phone from it
        $customer = Customer::where('external_customer_code', $customerCode)->first();
        if ($customer) {
            session(['selected_customer_id' => $customer->id]);

            // Backfill phone into session if it wasn't set (e.g. GLIMS multi-profile flow)
            if (! session('pending_phone') && $customer->phone) {
                session(['pending_phone' => $customer->phone]);
            }
        }

        // Use posted name first (fresh from GLIMS profile),
        // fall back to DB name, then session, then generic fallback
        $resolvedName = $request->input('name') ?? $customer?->name ?? session('pending_name') ?? 'there';

        // Only accept DB name if it's not a placeholder
        if (in_array($resolvedName, ['Unknown', 'there', ''])) {
            $resolvedName = $request->input('name') ?? 'there';
        }

        session(['pending_name' => $resolvedName]);

        $phone = session('pending_phone');

        if (! $phone) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No phone number found for this profile. Please contact support.',
            ], 422);
        }

        return $this->sendOtpAndRespond($phone, $resolvedName);
    }

    // New OTP without relying on Genova or Glims
    public function verifyOtpAjax(Request $request): JsonResponse
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

    // Resend that OTP
    public function resendOtp(Request $request): JsonResponse
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

    // ── PRIVATE: Complete login ────────────────────
    private function completeLogin(Customer $customer): void
    {
        Auth::guard('customer')->login($customer);

        // Grab secondary code BEFORE wiping the session
        $secondaryCode = session('pending_secondary_code');

        session()->forget([
            'pending_auth', 'pending_user_id', 'pending_phone',
            'pending_name', 'pending_customer_code',
            'pending_secondary_code',
            'selected_customer_id', 'auth_source', 'login_type', 'username',
        ]);

        // Fallback dispatch — covers brand-new customers who didn't exist
        // when sendOtpAndRespond fired the early dispatch.
        // SyncCustomerPoliciesJob skips if synced within last 30 min,
        // so dispatching twice is safe — the job deduplicates itself.
        try {
            SyncCustomerPoliciesJob::dispatch($customer, $secondaryCode);
        } catch (\Exception $e) {
            // Never let a sync failure block the login
            Log::error('completeLogin: sync job dispatch failed', [
                'customer_id' => $customer->id,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    // ── PRIVATE: Resolve profiles from Genova + GLIMS ─────────────────
    private function resolveProfiles(?string $phone, int $userId): array
    {
        if (! $phone) {
            return [];
        }

        // Index Genova profiles by normalised name
        $byName = [];
        foreach ($this->resolveGenovaProfiles($phone, $userId) as $profile) {
            $key                = mb_strtolower(trim($profile['name']));
            $profile['sources'] = [$profile['source']];
            $byName[$key]       = $profile;
        }

        // Merge GLIMS — same name = same person, absorb silently as secondary
        $existingCodes = collect($byName)->pluck('code');
        foreach ($this->resolveGlimsProfiles($phone) as $gp) {
            if ($existingCodes->contains($gp['code'])) {
                continue; // literally the same record, skip
            }

            $key = mb_strtolower(trim($gp['name']));

            if (array_key_exists($key, $byName)) {
                // Same person in both systems — merge, don't show twice
                $byName[$key]['secondary_code']   = $gp['code'];
                $byName[$key]['secondary_source'] = $gp['source'];
                $byName[$key]['sources'][]        = $gp['source'];
            } else {
                $gp['sources'] = [$gp['source']];
                $byName[$key]  = $gp;
            }
        }

        return array_values($byName);
    }

    // ── PRIVATE: Resolve profiles from Genova only ────────────────────
    private function resolveGenovaProfiles(?string $phone, int $userId): array
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
            Log::warning('resolveGenovaProfiles: Genova customer-search failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $profiles;
    }

    // PRIVATE: Resolve profiles from GLIMS only
    private function resolveGlimsProfiles(string $phone): array
    {
        $profiles = [];

        // Try middleware API first
        try {
            $profiles = $this->glims->resolveProfilesByPhone($phone);

            if (! empty($profiles)) {
                Log::info('resolveGlimsProfiles: found via API', [
                    'phone' => $phone,
                    'count' => count($profiles),
                ]);
                return $profiles;
            }
        } catch (\Exception $e) {
            Log::warning('resolveGlimsProfiles: API lookup failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // API returned nothing — fall back to local synced DB
        Log::info('resolveGlimsProfiles: API empty, falling back to local DB', ['phone' => $phone]);

        $localCustomer = Customer::where('phone', $phone)
            ->whereJsonContains('sources', 'glims')
            ->first();

        if ($localCustomer) {
            $profiles[] = [
                'code'         => $localCustomer->external_customer_code,
                'name'         => $localCustomer->name,
                'phone'        => $localCustomer->phone,
                'email'        => $localCustomer->email,
                'policy_count' => $localCustomer->policies()->count(),
                'source'       => 'glims_local',
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
                return ['success' => false, 'reason' => 'http_' . $response->status()];
            }

            $content = $response->json('data.content') ?? [];

            if (empty($content)) {
                return ['success' => false, 'reason' => 'empty_response'];
            }

            $first = $content[0];

            return [
                'success' => true,
                'data'    => [
                    'user_id'     => $first['customer_api_identity'] ?? null,
                    'name'        => $first['name'] ?? 'Unknown',
                    'search_used' => ['phone_no' => $first['phone_number'] ?? null],
                ],
            ];

        } catch (\Exception $e) {
            Log::error('attemptGenovaVerification: ' . $e->getMessage());
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

    private function sendOtpAndRespond(string $phone, string $name): JsonResponse
    {
        if (! $this->otp->send($phone)) {
            Log::error('sendOtpAndRespond: Failed to send OTP', ['phone' => $phone]);
            return response()->json([
                'status'  => 'error',
                'message' => 'We found your account but could not send the verification code. Please try again.',
            ], 500);
        }

        // Dispatch sync job early
        // Start syncing while the user is reading their SMS and entering OTP.
        // By the time they land on the dashboard, policies may already be ready.
        $customerCode = session('pending_customer_code');
        if ($customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)->first();
            if ($customer) {
                try {
                    SyncCustomerPoliciesJob::dispatch($customer);
                    Log::info('sendOtpAndRespond: early sync dispatched', [
                        'customer_id' => $customer->id,
                    ]);
                } catch (\Exception $e) {
                    Log::warning('sendOtpAndRespond: early sync dispatch failed', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }
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

        // 1. Always try by customer code first — this is the unique profile identifier
        if ($customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)->first();

            if ($customer) {
                $updates = [];

                if (empty($customer->phone)) {
                    $updates['phone'] = $phone;
                }

                // If name was previously saved as Unknown, fix it now
                if (empty($customer->name) || $customer->name === 'Unknown') {
                    $updates['name'] = $pendingName ?? $customer->name;
                }

                if (! empty($updates)) {
                    $customer->update($updates);
                }

                return $customer->fresh();
            }

            // Customer code exists but no DB record yet — create one
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

        // 2. No customer code at all — last resort phone lookup
        // Only used when auth source couldn't provide a code (edge case)
        Log::warning('resolveCustomerFromSession: no customer code in session, falling back to phone', [
            'phone' => $phone,
        ]);

        $customer = Customer::where('phone', $phone)
            ->where('external_customer_code', $customerCode) // won't match anything — safe guard
            ->first();

        if (! $customer) {
            Log::error('resolveCustomerFromSession: no customer code, cannot create', ['phone' => $phone]);
            return null;
        }

        return $customer;
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
