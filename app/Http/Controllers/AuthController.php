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

    // STEP 1 → Handle Login Request (request-claim-otp sends OTP immediately)
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'login_type' => 'sometimes|in:mobile_no,policy_number,vehicle_number'
        ]);

        try {
            $loginType = $request->input('login_type', 'mobile_no');
            
            // This endpoint verifies user AND sends OTP in one call
            $response = $this->api->customerVerification($request->username, $loginType);

            if ($response->failed()) {
                Log::error('Customer Verification Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'identifier' => $request->username,
                    'type' => $loginType
                ]);
                
                $errorMessage = 'Invalid credentials. Please try again.';
                
                if ($response->status() === 400) {
                    $body = $response->json();
                    $errorMessage = $body['message'] ?? $errorMessage;
                }
                
                return back()->withErrors([$errorMessage])->withInput();
            }

            $data = $response->json('data');

            // Store user info for OTP verification
            session([
                'user_id'       => $data['user_id'],
                'name'          => $data['name'],
                'username'      => $request->username,
                'login_type'    => $loginType,
                'search_used'   => $data['search_used'] ?? null,
                'sent_to'       => $data['sent_to'] ?? [],
            ]);

            Log::info('User verified and OTP sent', [
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'phone' => $data['search_used']['phone_no'] ?? null
            ]);

            // The message from API says where OTP was sent
            $successMessage = $data['message'] ?? 'OTP sent to your registered contact.';

            return redirect()->route('otp')->with('success', $successMessage);

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

    // STEP 2 → Resend OTP (calls request-claim-otp again)
    public function requestOtp()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
        }

        try {
            // Call the same endpoint again to resend OTP
            $response = $this->api->customerVerification(
                session('username'),
                session('login_type', 'mobile_no')
            );

            if ($response->failed()) {
                Log::error('Resend OTP Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return back()->withErrors(['Failed to resend OTP. Please try again.']);
            }

            $data = $response->json('data');
            Log::info('OTP resent successfully', ['user_id' => session('user_id')]);

            return back()->with('success', $data['message'] ?? 'OTP sent successfully!');

        } catch (\Exception $e) {
            Log::error('Resend OTP Error: ' . $e->getMessage());
            return back()->withErrors(['Failed to resend OTP: ' . $e->getMessage()]);
        }
    }

    // STEP 3 → Verify Claim OTP (FIXED: using correct endpoint)
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|string|min:6|max:6'
        ]);

        if (!session('user_id')) {
            return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
        }

        try {
            // Use the correct verify-claim-otp endpoint
            $response = $this->api->verifyClaimOtp(
                session('user_id'),
                $request->otp
            );

            if ($response->failed()) {
                Log::error('Verify Claim OTP Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'user_id' => session('user_id'),
                    'otp_length' => strlen($request->otp)
                ]);
                
                $errorMessage = 'Invalid OTP. Please try again.';
                
                if ($response->status() === 400) {
                    $body = $response->json();
                    $errorMessage = $body['message'] ?? $errorMessage;
                }
                
                return back()->withErrors([$errorMessage])->withInput();
            }

            // Get the response data
            $responseData = $response->json();
            
            Log::info('OTP Verified Successfully', [
                'user_id' => session('user_id'),
                'response' => $responseData
            ]);

            // Mark user as authenticated
            session(['authenticated' => true]);

            return redirect()->route('dashboard')->with('success', 'Login successful!');

        } catch (\Exception $e) {
            Log::error('Verify OTP Error: ' . $e->getMessage());
            return back()->withErrors(['Verification failed: ' . $e->getMessage()]);
        }
    }
}