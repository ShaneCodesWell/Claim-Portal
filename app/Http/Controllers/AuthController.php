<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GenovaApiService;
use Illuminate\Support\Facades\Log;

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

    // STEP 1 → Handle Login Request
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'login_type' => 'sometimes|in:mobile_no,policy_number,vehicle_number'
        ]);

        try {
            // Determine the type of identifier (default to mobile_no)
            $loginType = $request->input('login_type', 'mobile_no');
            
            $response = $this->api->customerVerification($request->username, $loginType);

            if ($response->failed()) {
                Log::error('Customer Verification Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'identifier' => $request->username,
                    'type' => $loginType
                ]);
                
                $errorMessage = 'Invalid credentials. Please try again.';
                
                // Parse API error if available
                if ($response->status() === 400) {
                    $body = $response->json();
                    $errorMessage = $body['message'] ?? $errorMessage;
                }
                
                return back()->withErrors([$errorMessage])->withInput();
            }

            $data = $response->json('data');

            // Store user info temporarily for OTP
            session([
                'user_id'       => $data['user_id'],
                'name'          => $data['name'],
                'username'      => $request->username,
                'login_type'    => $loginType,
                'search_used'   => $data['search_used'] ?? null,
            ]);

            Log::info('User verified, redirecting to OTP', [
                'user_id' => $data['user_id'],
                'name' => $data['name']
            ]);

            return redirect()->route('otp')->with('success', $data['message'] ?? 'OTP sent to your registered contact.');

        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return back()->withErrors(['Connection failed: ' . $e->getMessage()])->withInput();
        }
    }

    // STEP 2 → OTP Screen
    public function showOtpForm()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
        }
        return view('auth.otp');
    }

    // STEP 3 → Verify 2FA
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required|digits:6']);

        if (!session('user_id')) {
            return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
        }

        try {
            $response = $this->api->verify2FA(
                session('user_id'),
                $request->otp
            );

            if ($response->failed()) {
                Log::error('Verify 2FA Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return back()->withErrors(['Invalid OTP. Please try again.']);
            }

            // Mark user as fully authenticated
            session(['authenticated' => true]);

            Log::info('User authenticated successfully', ['user_id' => session('user_id')]);

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Verify OTP Error: ' . $e->getMessage());
            return back()->withErrors(['Verification failed: ' . $e->getMessage()]);
        }
    }
}