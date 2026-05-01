<?php
namespace App\Http\Controllers;

use App\Models\Customer;
use App\Services\GenovaApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use \Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    protected $api;

    public function __construct(GenovaApiService $api)
    {
        $this->api = $api;
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

    // STEP 2 → Show OTP Verification Form
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

    // STEP 3 → Verify OTP
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

        // Find the customer record — same lookup logic as the dashboard
        $customer = Customer::where('phone', $phoneNumber)
            ->orWhere('external_customer_code', $customerCode)
            ->orWhere('external_customer_id', session('user_id'))
            ->first();

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

    // public function staffLogin(Request $request)
    // {
    //     $request->validate([
    //         'email'    => 'required|email',
    //         'password' => 'required|string',
    //     ]);

    //     $credentials = $request->only('email', 'password');

    //     if (Auth::attempt($credentials)) {
    //         $request->session()->regenerate();
    //         return redirect()->intended('/admin/staff/dashboard');
    //     }

    //     return back()->withErrors([
    //         'email' => 'The provided credentials do not match our records.',
    //     ])->withInput();
    // }

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
                'Admin'           => redirect()->route('staff.claims.index'),
                default           => redirect()->route('staff.claims.index'),
            };
        }

        return back()->withErrors([
            'email' => 'These credentials do not match our records.',
        ])->onlyInput('email');
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
