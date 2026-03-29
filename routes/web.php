<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GeneralAccidentController;
use App\Http\Controllers\MotorFormController;
use App\Http\Controllers\OfflineController;
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

    // Motor Forms
    Route::get('/motor-form', [MotorFormController::class, 'index'])->name('motor-form');

    // General Accident Forms
    Route::get('/general-accident-form', [GeneralAccidentController::class, 'index'])->name('general-accident-form');

    // Fire Forms
    Route::get('/fire-form', [FireController::class, 'index'])->name('fire-form');
    Route::post('/fire-form/submit', [FireController::class, 'create'])->name('fire-form.submit');
});

// Test route for dashboard
Route::get('/offline-dashboard', [OfflineController::class, 'index'])->name('offline-dashboard');
Route::get('/offline-form', [OfflineController::class, 'form'])->name('offline-form');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
