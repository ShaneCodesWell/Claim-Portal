<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GeneralAccidentController;
use App\Http\Controllers\MotorFormController;
use App\Http\Controllers\OfflineController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BranchController;
use Illuminate\Support\Facades\Route;

// Auth Routes
Route::get('/', [AuthController::class, 'showUserSelectForm'])->name('user.select');

Route::get('/login-user', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/staff-login', [AuthController::class, 'staffLoginForm'])->name('staff.login');
Route::get('/agent-login', [AuthController::class, 'agentLogin'])->name('agent.login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/staff-login', [AuthController::class, 'staffLogin'])->name('staff.login.submit');

Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp');
Route::post('/otp/request', [AuthController::class, 'requestOtp'])->name('otp.send');
Route::get('/otp/verify-form', [AuthController::class, 'verifyOtpForm'])->name('otp.verify');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');
Route::get('/login/local', [AuthController::class, 'showLocalLoginForm'])->name('login.local');
Route::post('/login/local', [AuthController::class, 'localLogin'])->name('login.local.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Customers Routes
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

// Staff routes — accessible by ALL staff including admins
Route::middleware(['staff'])->prefix('admin')->group(function () {

    // Dashboard and Claims
    Route::get('/staff/dashboard', [StaffController::class, 'dashboard'])->name('staff-dashboard');
    Route::get('/staff/all-claims', [StaffController::class, 'allClaims'])->name('all-claims');
    // Route::get('/staff/process-claim', [StaffController::class, 'processClaim'])->name('process-claim');
    Route::get('/staff/process-claim/motor', [StaffController::class, 'processClaimMotor'])->name('process-claim-motor');
    Route::get('/staff/process-claim/fire', [StaffController::class, 'processClaimFire'])->name('process-claim-fire');
    Route::get('/staff/process-claim/general-accident', [StaffController::class, 'processClaimGeneralAccident'])->name('process-claim-general-accident');
    Route::get('/staff/my-claims', [StaffController::class, 'myClaims'])->name('my-claims');

    // Claim Forms & Documents
    Route::get('/staff/claim-forms', [StaffController::class, 'claimForms'])->name('claim-form');
    Route::get('/staff/claim-forms/create', [StaffController::class, 'createClaimForms'])->name('create-claim-form');
    Route::get('/staff/claim-documents', [StaffController::class, 'claimDouments'])->name('claim-documents');

    // Customers
    Route::get('/staff/customers', [StaffController::class, 'customers'])->name('customers');

});

// Admin-only routes — only admins can access
Route::middleware(['admin'])->prefix('admin')->group(function () {
    // Organization
    Route::get('/organization', [CompanyController::class, 'index'])->name('organization');

    // Settings
    Route::get('/settings', [CompanyController::class, 'settings'])->name('settings');
    Route::get('/settings/company', [CompanyController::class, 'edit'])->name('settings.company');
    Route::put('/settings/company', [CompanyController::class, 'update'])->name('settings.company.update');

    // Branch
    Route::get('/settings/branches', [BranchController::class, 'index'])->name('branches.index');
    Route::get('/settings/branches/create', [BranchController::class, 'create'])->name('branches.create');
    Route::post('/settings/branches', [BranchController::class, 'store'])->name('branches.store');
    Route::get('/settings/branches/{branch}/edit', [BranchController::class, 'edit'])->name('branches.edit');
    Route::put('/settings/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
    Route::delete('/settings/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
    
    // Staff Management
    Route::get('/settings/create-staff', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/settings/staff-store', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/settings/edit-staff/{staff}', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/settings/staff-update/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/settings/staff-delete/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');
});

// Offline Application
Route::prefix('offline')->name('offline.')->group(function () {
    Route::get('/dashboard', [OfflineController::class, 'index'])->name('dashboard');
    Route::get('/motor-form', [OfflineController::class, 'motorForm'])->name('motor-form');
    Route::get('/general-accident-form', [OfflineController::class, 'generalAccidentForm'])->name('general-accident-form');
    Route::get('/fire-form', [OfflineController::class, 'fireForm'])->name('fire-form');
});
