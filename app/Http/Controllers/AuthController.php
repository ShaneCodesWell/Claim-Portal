<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\GenovaApiService;
use App\Services\GlimsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use \Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    protected $api;
    protected $glims;

    public function __construct(GenovaApiService $api, GlimsService $glims)
    {
        $this->api   = $api;
        $this->glims = $glims;
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

    public function agentLogin()
    {
        return view('auth.agent-login');
    }

    // OLD login
    public function login(Request $request)
    {
        $request->validate([
            'username'   => 'required',
            'login_type' => 'sometimes|in:mobile_no,policy_number,vehicle_number',
        ]);

        try {
            $loginType = $request->input('login_type', 'mobile_no');

            $response = $this->api->customerVerification($request->username, $loginType);

            if ($response->failed()) {
                Log::error('Customer Verification Failed:', [
                    'status'     => $response->status(),
                    'body'       => $response->body(),
                    'identifier' => $request->username,
                    'type'       => $loginType,
                ]);

                // ── FALLBACK: API failed → redirect to local password login ──
                // Carry the identifier forward so the customer doesn't retype it.
                // Only carry the phone number since that's what local auth uses.
                $phoneForFallback = $loginType === 'mobile_no' ? $request->username : null;

                return redirect()
                    ->route('login.local')
                    ->with('fallback_reason', 'The verification service is currently unavailable. Please log in with your local password.')
                    ->with('prefill_phone', $phoneForFallback);
            }

            $data = $response->json('data');

            // Extract phone number from response
            $phoneNumber = null;
            if (isset($data['search_used']['phone_no'])) {
                $phoneNumber = $data['search_used']['phone_no'];
            } elseif ($loginType === 'mobile_no') {
                $phoneNumber = $request->username;
            }

            session([
                'user_id'           => $data['user_id'],
                'fullname'          => $data['name'],
                'name'              => $data['name'],
                'username'          => $request->username,
                'phone_number'      => $phoneNumber,
                'mobile_no'         => $phoneNumber,
                'login_type'        => $loginType,
                'search_used'       => $data['search_used'] ?? null,
                'sent_to'           => $data['sent_to'] ?? [],
                'customer_verified' => true,
            ]);
            session(['authenticated' => true]);

            Log::info('User verified and OTP sent', [
                'user_id' => $data['user_id'],
                'name'    => $data['name'],
                'phone'   => $phoneNumber,
            ]);

            $successMessage = $data['message'] ?? 'OTP sent to your registered contact.';

            // return redirect()->route('otp')->with('success', $successMessage); // ← Uncomment when OTP is live
            return redirect()->route('dashboard')->with('success', $successMessage);

        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());

            // Network/connection exception also falls back to local login
            return redirect()
                ->route('login.local')
                ->with('fallback_reason', 'Could not connect to the verification service. Please log in with your local password.')
                ->with('prefill_phone', $request->login_type === 'mobile_no' ? $request->username : null);
        }
    }

    // Show OTP Verification Form
    public function showOtpForm()
    {
        if (! session('user_id')) {
            return redirect()->route('login')->withErrors(['Please login first.']);
        }

        return view('auth.otp', [
            'name'    => session('name'),
            'sent_to' => session('sent_to'),
        ]);
    }

    // Verify OTP
    public function verifyOtpForm()
    {
        return view('auth.otp');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6',
        ]);

        try {
            $userId = session('user_id');
            $otp    = $request->otp;

            if (! $userId) {
                return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
            }

            $response = $this->api->verifyClaimOtp($userId, $otp);

            if ($response->failed()) {
                Log::error('OTP Verification Failed:', [
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);

                return back()->withErrors(['Invalid OTP. Please try again.'])->withInput();
            }

            $payload = $response->json();

            if (! isset($payload['data']) || ! is_array($payload['data'])) {
                Log::error('Unexpected Genova login payload', ['payload' => $payload]);
                return back()->withErrors(['We could not verify your details at the moment. Please try again.'])->withInput();
            }

            $data = $payload['data'];

            session([
                'customer_code' => $data['customer_code'] ?? null,
                'otp_verified'  => true,
            ]);

            Log::info('OTP verified successfully', [
                'user_id'       => $userId,
                'customer_code' => $data['customer_code'] ?? null,
            ]);

            return redirect()->route('dashboard')->with('success', 'Login successful!');

        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());
            return back()->withErrors(['Verification failed: ' . $e->getMessage()])->withInput();
        }
    }

    // Show the local password login form (Fallback when API is unavailable).
    public function showLocalLoginForm()
    {
        return view('auth.local-login', [
            'fallbackReason' => session('fallback_reason'),
            'prefillPhone'   => session('prefill_phone'),
        ]);
    }

    // Handle local password login.
    public function localLogin(Request $request)
    {
        $request->validate([
            'phone'    => 'required|string',
            'password' => 'required|string',
        ]);

        // Rate limit: 5 attempts per minute per IP
        $throttleKey = 'local-login:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'phone' => "Too many attempts. Please try again in {$seconds} seconds.",
            ]);
        }

        $customer = Customer::where('phone', $request->phone)->first();

        // Check customer exists and has a local password set
        if (! $customer || ! $customer->local_password) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'phone' => 'No local password found for this phone number. Please use the standard login or set up a password first.',
            ])->withInput();
        }

        // Verify the password
        if (! Hash::check($request->password, $customer->local_password)) {
            RateLimiter::hit($throttleKey, 60);

            return back()->withErrors([
                'password' => 'Incorrect password. Please try again.',
            ])->withInput();
        }

        RateLimiter::clear($throttleKey);

        // Set the same session keys the normal API flow sets
        // so the rest of the app (dashboard, middleware) works identically
        session([
            'authenticated'     => true,
            'user_id'           => $customer->external_customer_id,
            'fullname'          => $customer->name,
            'name'              => $customer->name,
            'phone_number'      => $customer->phone,
            'mobile_no'         => $customer->phone,
            'customer_code'     => $customer->external_customer_code,
            'customer_verified' => true,
            'auth_method'       => 'local', // so we know how they logged in
        ]);

        Log::info('Local password login successful', [
            'customer_id' => $customer->id,
            'phone'       => $customer->phone,
        ]);

        return redirect()->route('dashboard')->with('success', 'Logged in successfully.');
    }

    // Show the password setup page.
    public function showSetupPasswordForm()
    {
        if (! session('authenticated')) {
            return redirect()->route('login');
        }

        return view('auth.setup-password');
    }

    // Save the customer's local password.
    public function setupPassword(Request $request)
    {
        if (! session('authenticated')) {
            return redirect()->route('login');
        }

        $request->validate([
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one letter and one number.',
        ]);

        $phoneNumber  = session('phone_number') ?? session('mobile_no');
        $customerCode = session('customer_code');
        $userId       = session('user_id');

        $customer = null;

        if ($customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)->first();
        }

        if (! $customer && $phoneNumber) {
            $customer = Customer::where('phone', $phoneNumber)->first();
        }

        if (! $customer && $userId) {
            $customer = Customer::where('external_customer_id', (string) $userId)->first();
        }

        if (! $customer) {
            // Customer record doesn't exist yet (sync hasn't run)
            // Redirect to dashboard and let sync create the record first
            return redirect()->route('dashboard')
                ->with('error', 'Please wait for your account to finish loading, then try again.');
        }

        $customer->update([
            'local_password'        => Hash::make($request->password),
            'local_password_set_at' => now(),
        ]);

        Log::info('Local password set', ['customer_id' => $customer->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Password set successfully. You can now log in even when the system is offline.');
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

        $identifier = trim($request->username);
        $loginType  = $request->input('login_type', 'mobile_no');

        // ── STEP 1: Verify with Genova ────────────────────────────────
        $genovaResult = $this->attemptGenovaVerification($identifier, $loginType);

        if ($genovaResult['success']) {
            $data        = $genovaResult['data'];
            $userId      = $data['user_id'];
            $phoneNumber = $data['search_used']['phone_no'] ?? ($loginType === 'mobile_no' ? $identifier : null);

            // Store partial session — NOT fully authenticated yet
            session([
                'pending_auth'    => true,
                'pending_user_id' => $userId,
                'pending_phone'   => $phoneNumber,
                'pending_name'    => $data['name'],
                'login_type'      => $loginType,
                'username'        => $identifier,
            ]);

            // ── STEP 2: Resolve customer profiles ─────────────────────
            $profiles = $this->resolveProfiles($phoneNumber, $userId);

            if (empty($profiles)) {
                // Phone verified but no customer record found anywhere
                Log::error('Auth: phone verified but no profile found', [
                    'phone'      => $phoneNumber,
                    'user_id'    => $userId,
                    'login_type' => $loginType,
                ]);

                return response()->json([
                    'status'  => 'no_profile',
                    'message' => 'Your phone number was verified but we could not find an associated account. Please contact support.',
                ]);
            }

            if (count($profiles) === 1) {
                // Single profile — store it pending password
                session(['pending_customer_code' => $profiles[0]['code']]);

                return response()->json([
                    'status'  => 'single_profile',
                    'profile' => $profiles[0],
                    'message' => 'Profile found.',
                ]);
            }

            // Multiple profiles — let user pick
            return response()->json([
                'status'   => 'profile_selection',
                'profiles' => $profiles,
                'message'  => 'Multiple profiles found. Please select yours.',
            ]);
        }

        // ── Genova failed — try GLIMS ──────────────────────────────────
        Log::warning('loginAjax: Genova failed, trying GLIMS', [
            'identifier' => $identifier,
            'reason'     => $genovaResult['reason'],
        ]);

        if ($loginType === 'mobile_no') {
            $glimsProfiles = $this->resolveGlimsProfiles($identifier);

            if (! empty($glimsProfiles)) {
                session([
                    'pending_auth'  => true,
                    'pending_phone' => $identifier,
                    'pending_name'  => $glimsProfiles[0]['name'],
                    'login_type'    => $loginType,
                    'auth_source'   => 'glims',
                ]);

                if (count($glimsProfiles) === 1) {
                    session(['pending_customer_code' => $glimsProfiles[0]['code']]);

                    return response()->json([
                        'status'  => 'single_profile',
                        'profile' => $glimsProfiles[0],
                        'source'  => 'glims',
                    ]);
                }

                return response()->json([
                    'status'   => 'profile_selection',
                    'profiles' => $glimsProfiles,
                    'source'   => 'glims',
                ]);
            }
        }

        // ── Both failed — check local password fallback ────────────────
        if ($loginType === 'mobile_no') {
            $localCustomer = Customer::where('phone', $identifier)
                ->whereNotNull('local_password')
                ->first();

            if ($localCustomer) {
                return response()->json([
                    'status'  => 'local_password_available',
                    'message' => 'Verification service unavailable. Please log in with your local password.',
                    'phone'   => $identifier,
                ]);
            }
        }

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

        // Store the confirmed profile
        session(['pending_customer_code' => $customerCode]);

        // Check password status
        $customer = Customer::where('external_customer_code', $customerCode)->first();

        if (! $customer || empty($customer->local_password)) {
            return response()->json([
                'status'  => 'needs_password_setup',
                'message' => 'Please set a local password to secure your account.',
            ]);
        }

        return response()->json([
            'status'  => 'needs_password_entry',
            'message' => 'Please enter your password to continue.',
        ]);
    }

    // ── STEP 3a: Enter existing password ─────────────────────────────
    public function enterPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session('pending_auth')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired.'], 401);
        }

        $request->validate(['password' => 'required|string']);

        $customerCode = session('pending_customer_code');
        $phone        = session('pending_phone');

        $customer = null;
        if ($customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)->first();
        }
        if (! $customer && $phone) {
            $customer = Customer::where('phone', $phone)->first();
        }

        if (! $customer) {
            return response()->json(['status' => 'error', 'message' => 'Account not found.'], 404);
        }

        // Rate limit
        $throttleKey = 'enter-password:' . $request->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return response()->json([
                'status'  => 'error',
                'message' => "Too many attempts. Try again in {$seconds} seconds.",
            ], 429);
        }

        if (! Hash::check($request->password, $customer->local_password)) {
            RateLimiter::hit($throttleKey, 60);
            return response()->json([
                'status'  => 'error',
                'message' => 'Incorrect password. Please try again.',
            ], 422);
        }

        RateLimiter::clear($throttleKey);

        // ── Password correct — complete the session ────────────────────
        $this->completeLogin($customer);

        return response()->json([
            'status'   => 'success',
            'redirect' => route('dashboard'),
        ]);
    }

    // ── STEP 3b: Setup password (new users) ──────────────────────────
    public function setupPasswordAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session('pending_auth') && ! session('authenticated')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired.'], 401);
        }

        $request->validate([
            'password' => [
                'required', 'string', 'min:8', 'confirmed',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/',
            ],
        ], ['password.regex' => 'Password must contain at least one letter and one number.']);

        $customerCode = session('pending_customer_code') ?? session('customer_code');
        $phone        = session('pending_phone') ?? session('phone_number') ?? session('mobile_no');

        $customer = null;
        if ($customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)->first();
        }
        if (! $customer && $phone) {
            $customer = Customer::where('phone', $phone)->first();
        }

        if (! $customer) {
            // Customer not in local DB yet — create them now from pending session
            if ($customerCode) {
                $customer = Customer::create([
                    'external_customer_code' => $customerCode,
                    'name'                   => session('pending_name') ?? 'Unknown',
                    'phone'                  => $phone,
                    'sources'                => ['genova'],
                ]);

                Log::info('setupPasswordAjax: created new customer record', [
                    'customer_code' => $customerCode,
                    'phone'         => $phone,
                ]);
            } else {
                // Truly nothing to work with
                return response()->json([
                    'status'   => 'success',
                    'message'  => 'Logged in. Set your password from the dashboard.',
                    'redirect' => route('dashboard'),
                ]);
            }
        }

        $customer->update([
            'local_password'        => Hash::make($request->password),
            'local_password_set_at' => now(),
        ]);

        Log::info('setupPasswordAjax: password set', ['customer_id' => $customer->id]);

// ── Complete the login now ──
        $this->completeLogin($customer);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Password saved! Taking you to your dashboard.',
            'redirect' => route('dashboard'),
        ]);

        Log::info('setupPasswordAjax: password set', ['customer_id' => $customer->id]);

        // Complete login after setup
        $this->completeLogin($customer);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Password saved! Taking you to your dashboard.',
            'redirect' => route('dashboard'),
        ]);
    }

    // ── PRIVATE: Complete login — set full session ────────────────────
    private function completeLogin(Customer $customer): void
    {
        $pendingName = session('pending_name');

        session()->forget(['pending_auth', 'pending_user_id', 'pending_phone',
            'pending_name', 'pending_customer_code']);

        session([
            'authenticated'     => true,
            'user_id'           => $customer->external_customer_id ?? $customer->external_customer_code,
            'fullname'          => $customer->name ?? $pendingName,
            'name'              => $customer->name ?? $pendingName,
            'phone_number'      => $customer->phone,
            'mobile_no'         => $customer->phone,
            'customer_code'     => $customer->external_customer_code,
            'customer_verified' => true,
            'auth_source'       => $customer->sources
                ? (in_array('genova', $customer->sources) ? 'genova' : 'glims')
                : 'genova',
        ]);

        Log::info('completeLogin: session established', [
            'customer_id'   => $customer->id,
            'customer_code' => $customer->external_customer_code,
            'phone'         => $customer->phone,
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

        try {
            // Skip GLIMS lookup if not reachable — avoids 20s timeout off-premise
            if (! $this->glims->isConnected()) {
                return $profiles;
            }
            
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
            }
        } catch (\Exception $e) {
            Log::warning('resolveGlimsProfiles failed', ['error' => $e->getMessage()]);
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
     * Attempt GLIMS verification, return a normalised result array.
     * Maps login_type to the right GLIMS lookup.
     */
    private function attemptGlimsVerification(string $identifier, string $loginType): array
    {
        try {
            // GlimsService::customerVerification currently searches by CLIENT_CODE.
            // For phone lookups we need a phone-based query — handle both cases.
            $customer = null;

            if ($loginType === 'mobile_no') {
                // Phone search — query directly since GlimsService defaults to CLIENT_CODE
                $customer = $this->glimsPhoneLookup($identifier);
            } else {
                // Policy / vehicle number — use existing method with the identifier as client code
                // This will expand as GLIMS lookup methods are added
                $customer = $this->glims->customerVerification($identifier, $loginType);
            }

            if (! $customer) {
                return ['success' => false, 'reason' => 'not_found'];
            }

            return ['success' => true, 'customer' => $customer];

        } catch (\Exception $e) {
            Log::error('attemptGlimsVerification exception: ' . $e->getMessage());
            return ['success' => false, 'reason' => 'exception'];
        }
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

    /**
     * Find an existing Customer record or create a minimal one from GLIMS data.
     *
     * We search by external_customer_code first, then fall back to phone.
     * This covers the common case where the record already exists from a
     * Genova sync — it may not have external_customer_code set yet but
     * will almost certainly have the phone number.
     */
    private function findOrCreateCustomerFromGlims(array $glimsCustomer, ?string $phone, string $name): Customer
    {
        $clientCode = $glimsCustomer['CLIENT_CODE'];

        // Search by code first, then phone — separately, not in one orWhere
        $existing = Customer::where('external_customer_code', $clientCode)->first();

        if (! $existing && $phone) {
            $existing = Customer::where('phone', $phone)->first();
        }

        if ($existing) {
            // Only fill fields that are genuinely missing — never touch local_password
            $updates = [];

            if (empty($existing->name)) {
                $updates['name'] = $name;
            }

            if (empty($existing->phone)) {
                $updates['phone'] = $phone;
            }

            if (empty($existing->external_customer_code)) {
                $updates['external_customer_code'] = $clientCode;
            }

            if (empty($existing->external_customer_id)) {
                $updates['external_customer_id'] = $clientCode;
            }

            if (empty($existing->email)) {
                $updates['email'] = $glimsCustomer['CLIENT_HOME_EMAIL'] ?? null;
            }

            if (! empty($updates)) {
                $existing->update($updates);
            }

            // Always re-fetch fresh from DB so local_password is definitely loaded
            return $existing->fresh();
        }

        return Customer::create([
            'external_customer_code' => $clientCode,
            'external_customer_id'   => $clientCode,
            'name'                   => $name,
            'phone'                  => $phone,
            'email'                  => $glimsCustomer['CLIENT_HOME_EMAIL'] ?? null,
            'sources'                => ['glims'],
        ]);
    }

    /**
     * Check whether the customer (found via Genova) still needs to set a local password.
     *
     * We search by every identifier we have so a record created during a previous
     * sync (phone, external_customer_id, or external_customer_code) is always found.
     * If genuinely no record exists yet (very first ever login, sync not run),
     * we return true so the setup prompt appears — but that is the correct behaviour
     * in that case. The false-positive was caused by a narrow lookup that missed
     * existing records, which is fixed by the broader WHERE below.
     */
    private function customerNeedsPasswordSetup(?string $phone, array $genovaData): bool
    {
        $userId       = $genovaData['user_id'] ?? null;
        $customerCode = $genovaData['search_used']['client_code'] ?? $genovaData['customer_code'] ?? null;

        // Search separately — not in one orWhere — to avoid query builder quirks
        $customer = null;

        if ($phone) {
            $customer = Customer::where('phone', $phone)
                ->whereNotNull('local_password')->first();
        }

        if (! $customer && $customerCode) {
            $customer = Customer::where('external_customer_code', $customerCode)
                ->whereNotNull('local_password')->first();
        }

        if (! $customer && $userId) {
            $customer = Customer::where('external_customer_id', $userId)
                ->whereNotNull('local_password')->first();
        }

        Log::debug('customerNeedsPasswordSetup result', [
            'phone'         => $phone,
            'customer_code' => $genovaData['customer_code'] ?? null,
            'user_id'       => $genovaData['user_id'] ?? null,
            'found'         => $customer ? $customer->id : 'NOT FOUND',
            'has_password'  => $customer ? ! empty($customer->local_password) : false,
        ]);

        return $customer === null;
    }

    // Logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // Dismiss the "set your local password" nudge for the current session.
    public function dismissNudge()
    {
        session(['nudge_dismissed' => true]);
        return response()->json(['ok' => true]);
    }

}
