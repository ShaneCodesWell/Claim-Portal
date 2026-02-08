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

            // Extract phone number from search_used
            $phoneNumber = null;
            if (isset($data['search_used']['phone_no'])) {
                $phoneNumber = $data['search_used']['phone_no'];
            } elseif ($loginType === 'mobile_no') {
                $phoneNumber = $request->username;
            }

            // Store comprehensive user info for OTP verification and dashboard
            session([
                'user_id'       => $data['user_id'],
                'fullname'      => $data['name'],
                'name'          => $data['name'],
                'username'      => $request->username,
                'phone_number'  => $phoneNumber,
                'mobile_no'     => $phoneNumber,
                'login_type'    => $loginType,
                'search_used'   => $data['search_used'] ?? null,
                'sent_to'       => $data['sent_to'] ?? [],
                'customer_verified' => true,
            ]);
            session(['authenticated' => true]);

            Log::info('User verified and OTP sent', [
                'user_id' => $data['user_id'],
                'name' => $data['name'],
                'phone' => $phoneNumber
            ]);

            // The message from API says where OTP was sent
            $successMessage = $data['message'] ?? 'OTP sent to your registered contact.';

            // return redirect()->route('otp')->with('success', $successMessage); // Uncomment this when OTP verification is available
            return redirect()->route('dashboard')->with('success', $successMessage);
        } catch (\Exception $e) {
            Log::error('Login Error: ' . $e->getMessage());
            return back()->withErrors(['Connection failed: ' . $e->getMessage()])->withInput();
        }
    }

    // STEP 2 → Show OTP Verification Form
    public function showOtpForm()
    {
        if (!session('user_id')) {
            return redirect()->route('login')->withErrors(['Please login first.']);
        }

        return view('auth.otp', [
            'name' => session('name'),
            'sent_to' => session('sent_to')
        ]);
    }

    // STEP 2 → Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => 'required|digits:6'
        ]);

        try {
            $userId = session('user_id');
            $otp = $request->otp;

            if (!$userId) {
                return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
            }

            $response = $this->api->verifyClaimOtp($userId, $otp);

            if ($response->failed()) {
                Log::error('OTP Verification Failed:', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                return back()->withErrors(['Invalid OTP. Please try again.'])->withInput();
            }

            $payload = $response->json();

            if (!isset($payload['data']) || !is_array($payload['data'])) {
                Log::error('Unexpected Genova login payload', [
                    'payload' => $payload
                ]);

                return back()->withErrors([
                    'We could not verify your details at the moment. Please try again.'
                ])->withInput();
            }

            $data = $payload['data'];

            // Update session with additional customer data
            session([
                'customer_code' => $data['customer_code'] ?? null,
                'otp_verified' => true,
            ]);

            Log::info('OTP verified successfully', [
                'user_id' => $userId,
                'customer_code' => $data['customer_code'] ?? null
            ]);

            return redirect()->route('dashboard')->with('success', 'Login successful!');
        } catch (\Exception $e) {
            Log::error('OTP Verification Error: ' . $e->getMessage());
            return back()->withErrors(['Verification failed: ' . $e->getMessage()])->withInput();
        }
    }

    // Logout
    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Logged out successfully.');
    }

    // STEP 2 → OTP Screen
    // public function showOtpForm()
    // {
    //     if (!session('user_id')) {
    //         return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
    //     }
    //     return view('auth.otp');
    // }

    // STEP 3 → Verify Claim OTP (FIXED: using correct endpoint)
    // public function verifyOtp(Request $request)
    // {
    //     $request->validate([
    //         'otp' => 'required|string|min:6|max:6'
    //     ]);

    //     if (!session('user_id')) {
    //         return redirect()->route('login')->withErrors(['Session expired. Please login again.']);
    //     }

    //     try {
    //         // Use the correct verify-claim-otp endpoint
    //         $response = $this->api->verifyClaimOtp(
    //             session('user_id'),
    //             $request->otp
    //         );

    //         if ($response->failed()) {
    //             Log::error('Verify Claim OTP Failed:', [
    //                 'status' => $response->status(),
    //                 'body' => $response->body(),
    //                 'user_id' => session('user_id'),
    //                 'otp_length' => strlen($request->otp)
    //             ]);

    //             $errorMessage = 'Invalid OTP. Please try again.';

    //             if ($response->status() === 400) {
    //                 $body = $response->json();
    //                 $errorMessage = $body['message'] ?? $errorMessage;
    //             }

    //             return back()->withErrors([$errorMessage])->withInput();
    //         }

    //         // Get the response data
    //         $responseData = $response->json();

    //         Log::info('OTP Verified Successfully', [
    //             'user_id' => session('user_id'),
    //             'response' => $responseData
    //         ]);

    //         // Mark user as authenticated
    //         session(['authenticated' => true]);

    //         return redirect()->route('dashboard')->with('success', 'Login successful!');

    //     } catch (\Exception $e) {
    //         Log::error('Verify OTP Error: ' . $e->getMessage());
    //         return back()->withErrors(['Verification failed: ' . $e->getMessage()]);
    //     }
    // }
}
