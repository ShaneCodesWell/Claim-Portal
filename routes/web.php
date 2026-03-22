<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GeneralAccidentController;
use App\Http\Controllers\MotorFormController;
use Illuminate\Support\Facades\Route;



Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp');
Route::post('/otp/request', [AuthController::class, 'requestOtp'])->name('otp.send');

Route::get('/otp/verify-form', [AuthController::class, 'verifyOtpForm'])->name('otp.verify');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');

Route::get('/login/local', [AuthController::class, 'showLocalLoginForm'])->name('login.local');
Route::post('/login/local', [AuthController::class, 'localLogin'])->name('login.local.submit');

Route::middleware('auth.customer')->group(function () {
    // Customer Auth
    Route::get('/setup-password', [AuthController::class, 'showSetupPasswordForm'])->name('password.setup');
    Route::post('/setup-password', [AuthController::class, 'setupPassword'])->name('password.setup.submit');
    
    // Nudge
    Route::post('/nudge/dismiss', [AuthController::class, 'dismissNudge'])->name('nudge.dismiss');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/sync-policies', [DashboardController::class, 'syncPolicies'])->name('dashboard.sync');

    // Forms
    Route::get('/motor-form', [MotorFormController::class, 'index'])->name('motor-form');
    Route::get('/general-accident-form', [GeneralAccidentController::class, 'index'])->name('general-accident-form');
    Route::post('/motor-form/submit', [MotorFormController::class, 'store'])->name('motor-form.submit');
});

// Test route for dashboard
Route::get('/test-dashboard', [DashboardController::class, 'index'])->name('test-dashboard');
Route::get('/test-form', [DashboardController::class, 'form'])->name('test-form');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');