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
    public function loginAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'username'   => 'required|string',
            'login_type' => 'sometimes|in:mobile_no,policy_number,vehicle_number',
        ]);

        $identifier = trim($request->username);
        $loginType  = $request->input('login_type', 'mobile_no');

        // ── STEP 1: Try Genova ───────────────────────────────────
        $genovaResult = $this->attemptGenovaVerification($identifier, $loginType);

        if ($genovaResult['success']) {
            $data         = $genovaResult['data'];
            $phoneNumber  = $data['search_used']['phone_no'] ?? null;
            $customerCode = $data['search_used']['client_code'] ?? null;

            // ── Resolve phone + customer code for non-phone logins ──
            if (! $phoneNumber && $loginType !== 'mobile_no') {
                try {
                    // Use customer_id to look up by customer_id type
                    $policyResponse = $this->api->getPolicies(
                        $data['user_id'],
                        'customer_id' // ← use user_id, not the policy number
                    );

                    if ($policyResponse->successful()) {
                        $content = $policyResponse->json('data.content') ?? [];
                        if (! empty($content)) {
                            $first        = $content[0];
                            $phoneNumber  = $first['phone_number'] ?? null;
                            $customerCode = $customerCode ?? $first['code'] ?? null;

                            Log::info('Resolved phone from user_id lookup', [
                                'user_id'       => $data['user_id'],
                                'phone'         => $phoneNumber,
                                'customer_code' => $customerCode,
                            ]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('Could not resolve phone from policy login', [
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            session([
                'authenticated'     => true,
                'user_id'           => $data['user_id'],
                'fullname'          => $data['name'],
                'name'              => $data['name'],
                'username'          => $identifier,
                'phone_number'      => $phoneNumber,
                'mobile_no'         => $phoneNumber,
                'login_type'        => $loginType,
                'search_used'       => $data['search_used'] ?? null,
                'sent_to'           => $data['sent_to'] ?? [],
                'customer_verified' => true,
                'auth_source'       => 'genova',
                'customer_code'     => $customerCode,
            ]);

            Log::debug('Genova password check', [
                'phone'                 => $phoneNumber,
                'customer_code'         => $customerCode,
                'user_id'               => $data['user_id'],
                'customer_code_in_data' => $data['search_used']['client_code'] ?? 'NOT IN search_used',
            ]);

            $needsPasswordSetup = $this->customerNeedsPasswordSetup($phoneNumber, array_merge($data, [
                'customer_code' => $customerCode,
            ]));

            return response()->json([
                'status'               => 'success',
                'source'               => 'genova',
                'message'              => $data['message'] ?? 'Verification successful.',
                'name'                 => $data['name'],
                'needs_password_setup' => $needsPasswordSetup,
                'redirect'             => $needsPasswordSetup ? null : route('dashboard'),
            ]);
        }

        // ── STEP 2: Genova failed — try GLIMS silently ───────────
        Log::warning('loginAjax: Genova failed, falling back to GLIMS', [
            'identifier' => $identifier,
            'login_type' => $loginType,
            'reason'     => $genovaResult['reason'],
        ]);

        $glimsResult = $this->attemptGlimsVerification($identifier, $loginType);

        if ($glimsResult['success']) {
            $customer    = $glimsResult['customer'];
            $phoneNumber = $customer['CLIENT_HOME_MOBILE'] ?? $customer['CLIENT_HOME_TEL'] ?? null;

            // Build name from GLIMS parts
            $name = trim(implode(' ', array_filter([
                $customer['CLIENT_FIRST_NAME'] ?? null,
                $customer['CLIENT_MIDDLE_NAME'] ?? null,
                $customer['CLIENT_FAMILY_NAME'] ?? null,
            ])));

            // Find or create the local Customer record so we can check/set password.
            // IMPORTANT: firstOrCreate returns the *existing* record if one is found,
            // so local_password will be populated if the customer already set one.
            // We then refresh() to make sure we have the latest DB state in case
            // the record was just touched by a concurrent sync.
            $dbCustomer = $this->findOrCreateCustomerFromGlims($customer, $phoneNumber, $name);
            // $dbCustomer->refresh();

            session([
                'authenticated'     => true,
                'user_id'           => $customer['CLIENT_CODE'], // use CLIENT_CODE as user id for GLIMS users
                'fullname'          => $name,
                'name'              => $name,
                'username'          => $identifier,
                'phone_number'      => $phoneNumber,
                'mobile_no'         => $phoneNumber,
                'login_type'        => $loginType,
                'customer_verified' => true,
                'auth_source'       => 'glims',
                'customer_code'     => $customer['CLIENT_CODE'],
                'glims_client_code' => $customer['CLIENT_CODE'],
            ]);

            Log::info('loginAjax: GLIMS fallback success', [
                'client_code'        => $customer['CLIENT_CODE'],
                'name'               => $name,
                'has_local_password' => ! empty($dbCustomer->local_password),
            ]);

            // GLIMS users ALWAYS need password setup if they don't have one
            // They can't rely on Genova OTP next time since Genova is unreliable
            $needsPasswordSetup = empty($dbCustomer->local_password);

            // return response()->json([
            //     'status'               => 'success',
            //     'source'               => 'glims',
            //     'message'              => 'We found your account. Please set up a local password to secure your access.',
            //     'name'                 => $name,
            //     'needs_password_setup' => $needsPasswordSetup,
            //     'redirect'             => $needsPasswordSetup ? null : route('dashboard'),
            //     // Tell the frontend why we used GLIMS — so it can show appropriate messaging
            //     'fallback_notice'      => true,
            // ]);
            return response()->json([
                'status'               => 'success',
                'source'               => 'glims',
                'message'              => 'Account verified.',
                'name'                 => $name,
                'needs_password_setup' => $needsPasswordSetup,
                'redirect'             => $needsPasswordSetup ? null : route('dashboard'), // ← add this
                'fallback_notice'      => true,
            ]);

        }

        // ── STEP 3: Both Genova and GLIMS failed ─────────────────
        // Check if this phone number has a local password we can use
        if ($loginType === 'mobile_no') {
            $localCustomer = Customer::where('phone', $identifier)
                ->whereNotNull('local_password')
                ->first();

            if ($localCustomer) {
                Log::info('loginAjax: Both APIs failed, local password available', [
                    'phone' => $identifier,
                ]);

                // Don't log them in — but tell the frontend to show the password field
                return response()->json([
                    'status'  => 'local_password_available',
                    'message' => 'Our verification service is currently unavailable. You can still log in with your local password.',
                    'phone'   => $identifier,
                ]);
            }
        }

        // Truly failed — nothing available
        Log::error('loginAjax: All auth sources failed', [
            'identifier' => $identifier,
            'login_type' => $loginType,
        ]);

        return response()->json([
            'status'  => 'error',
            'message' => 'We could not verify your identity at the moment. Please try again shortly or contact support.',
        ], 422);
    }

    // ── 3. NEW: AJAX PASSWORD SETUP ──────────────────────────────
    // Called from the modal's password setup step.
    // Saves the password and returns a redirect URL on success.

    public function setupPasswordAjax(Request $request): \Illuminate\Http\JsonResponse
    {
        if (! session('authenticated')) {
            return response()->json(['status' => 'error', 'message' => 'Session expired. Please log in again.'], 401);
        }

        $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/',
            ],
        ], [
            'password.regex' => 'Password must contain at least one letter and one number.',
        ]);

        $phoneNumber  = session('phone_number') ?? session('mobile_no');
        $customerCode = session('customer_code');
        $userId       = session('user_id');

        // ── Strict sequential lookup — never use orWhere across all fields ──
        // Priority: customer_code (most specific) → phone → user_id
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
            Log::warning('setupPasswordAjax: Customer record not found', [
                'phone'         => $phoneNumber,
                'customer_code' => $customerCode,
                'user_id'       => $userId,
            ]);

            return response()->json([
                'status'   => 'success',
                'message'  => 'Logged in. Your account is still being set up — you can set a password from your dashboard.',
                'redirect' => route('dashboard'),
            ]);
        }

        $customer->update([
            'local_password'        => Hash::make($request->password),
            'local_password_set_at' => now(),
        ]);

        Log::info('setupPasswordAjax: Password set successfully', [
            'customer_id'   => $customer->id,
            'customer_code' => $customer->external_customer_code,
            'phone'         => $customer->phone,
        ]);

        return response()->json([
            'status'   => 'success',
            'message'  => 'Password saved! Taking you to your dashboard.',
            'redirect' => route('dashboard'),
        ]);
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
