<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MotorFormController;
use Illuminate\Support\Facades\Route;



Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp');
Route::post('/otp/request', [AuthController::class, 'requestOtp'])->name('otp.send');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify');

Route::middleware('auth.customer')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    // Route::get('/motor-form', [MotorFormController::class, 'index'])->name('motor-form');
});

// Test route for dashboard
Route::get('/test-dashboard', [DashboardController::class, 'index'])->name('test-dashboard');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');