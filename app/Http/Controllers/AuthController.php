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
            'username' => 'required'
        ]);

        $response = $this->api->customerLogin($request->username);

        if ($response->failed()) {
            return back()->withErrors(['Invalid user. Try again.'])->withInput();
        }

        $data = $response->json('data');

        // Store user info temporarily for OTP
        session([
            'user_id'       => $data['user_id'],
            'email'         => $data['email'],
            'fullname'      => $data['fullname'],
            'customer_code' => $data['customer_code'],
            'username'      => $request->username,
        ]);

        return redirect()->route('otp');
    }

    // STEP 2 → OTP Screen
    public function showOtpForm()
    {
        return view('auth.otp');
    }

    // STEP 2 → Send OTP
    public function requestOtp()
    {
        $response = $this->api->requestOtp(
            session('email'),
            session('user_id')
        );

        if ($response->failed()) {
            return back()->withErrors(['Failed to send OTP. Please try again.']);
        }

        return back()->with('success', 'OTP sent successfully!');
    }

    // STEP 3 → Verify OTP
    public function verifyOtp(Request $request)
    {
        $request->validate(['otp' => 'required']);

        $response = $this->api->verifyOtp(
            session('user_id'),
            $request->otp
        );

        if ($response->failed()) {
            return back()->withErrors(['Invalid OTP. Please try again.']);
        }

        // Mark user as fully authenticated
        session(['authenticated' => true]);

        return redirect()->route('dashboard');
    }
}