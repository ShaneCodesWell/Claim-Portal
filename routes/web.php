<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GeneralAccidentController;
use App\Http\Controllers\MotorFormController;
use App\Http\Controllers\OfflineController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Staff\GlimsSyncController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Staff\ClaimController as StaffClaimController;
use \App\Http\Controllers\Agent\ClaimController as AgentClaimController;
use \App\Http\Controllers\Customer\ClaimController as CustomerClaimController;

// Auth Routes
Route::get('/', [AuthController::class, 'showUserSelectForm'])->name('user.select');

Route::get('/login-user', [AuthController::class, 'showLoginForm'])->name('login');
Route::get('/staff-login', [AuthController::class, 'staffLoginForm'])->name('staff.login');
Route::get('/agent-login', [AuthController::class, 'agentLoginForm'])->name('agent.login');

Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/staff-login', [AuthController::class, 'staffLogin'])->name('staff.login.submit');
Route::post('/agent-login', [AuthController::class, 'agentLogin'])->name('agent.login.submit');

// AJAX password setup — called from the modal's password setup - multi-step login
Route::post('login/ajax', [AuthController::class, 'loginAjax'])->name('login.ajax');
Route::post('login/select-profile', [AuthController::class, 'selectProfile'])->name('login.select.profile');
Route::post('login/enter-password', [AuthController::class, 'enterPassword'])->name('login.enter.password');
Route::post('login/local', [AuthController::class, 'localLogin'])->name('login.local.submit');
Route::post('setup-password/ajax', [AuthController::class, 'setupPasswordAjax'])->name('setup.password.ajax');

Route::get('/otp', [AuthController::class, 'showOtpForm'])->name('otp');
Route::post('/otp/request', [AuthController::class, 'requestOtp'])->name('otp.send');
Route::get('/otp/verify-form', [AuthController::class, 'verifyOtpForm'])->name('otp.verify');
Route::post('/otp/verify', [AuthController::class, 'verifyOtp'])->name('otp.verify.submit');
Route::get('/login/local', [AuthController::class, 'showLocalLoginForm'])->name('login.local');
Route::post('/login/local', [AuthController::class, 'localLogin'])->name('login.local.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Forms - Publicy accessibly by everyone
Route::get('/motor-form', [MotorFormController::class, 'index'])->name('motor-form');
Route::get('/general-accident-form', [GeneralAccidentController::class, 'index'])->name('general-accident-form');
Route::get('/fire-form', [FireController::class, 'index'])->name('fire-form');

Route::prefix('glims')->name('staff.glims.')->group(function () {
    Route::post('sync/trigger', [GlimsSyncController::class, 'trigger'])->name('sync.trigger');
    Route::get('sync/status', [GlimsSyncController::class, 'status'])->name('sync.status');
});

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

    // Claims
    Route::post('claims', [CustomerClaimController::class, 'store'])->name('claims.store');
    Route::get('claims', [CustomerClaimController::class, 'index'])->name('claims.index');
    Route::get('claims/show/{claim}', [CustomerClaimController::class, 'show'])->name('claims.show');
    Route::get('claims/edit/{claim}', [CustomerClaimController::class, 'edit'])->name('claims.edit');
    Route::put('claims/update/{claim}', [CustomerClaimController::class, 'update'])->name('claims.update');

    // Preview Document
    Route::get('/documents/{document}/preview', [CustomerClaimController::class, 'previewDocument'])->name('customer.documents.preview');

});

// Staff routes — accessible by ALL staff including admins
Route::middleware(['staff'])->prefix('admin')->group(function () {

    // Claims
    Route::get('claims', [StaffClaimController::class, 'index'])->name('staff.claims.index');
    Route::get('claims/my-queue', [StaffClaimController::class, 'myQueue'])->name('staff.claims.my-queue');
    Route::get('claims/{claim}', [StaffClaimController::class, 'show'])->name('staff.claims.show');
    Route::get('/documents/{document}/preview', [StaffClaimController::class, 'previewDocument'])->name('staff.documents.preview');
    Route::get('claims/{claim}/print', [StaffClaimController::class, 'print'])->name('staff.claims.print');
    Route::post('claims/{claim}/assign', [StaffClaimController::class, 'assign'])->name('staff.claims.assign');
    Route::post('claims/{claim}/status', [StaffClaimController::class, 'updateStatus'])->name('staff.claims.status');
    Route::post('claims/{claim}/request-info', [StaffClaimController::class, 'requestInfo'])->name('staff.claims.request-info');
    Route::post('claims/{claim}/form-data', [StaffClaimController::class, 'updateFormData'])->name('staff.claims.form-data');

    Route::get('claims/{claim}/edit', [StaffClaimController::class, 'edit'])->name('staff.claims.edit');
    Route::put('claims/{claim}/edit', [StaffClaimController::class, 'update'])->name('staff.claims.update');
    Route::post('claims/{claim}/cancel', [StaffClaimController::class, 'cancel'])->name('staff.claims.cancel');

    Route::get('/staff/process-claim/motor', [StaffController::class, 'processClaimMotor'])->name('process-claim-motor');
    Route::get('/staff/process-claim/fire', [StaffController::class, 'processClaimFire'])->name('process-claim-fire');
    Route::get('/staff/process-claim/general-accident', [StaffController::class, 'processClaimGeneralAccident'])->name('process-claim-general-accident');

    // Claim Forms & Documents
    Route::get('/staff/claim-forms', [StaffController::class, 'claimForms'])->name('claim-form');
    Route::get('/staff/claim-forms/create', [StaffController::class, 'createClaimForms'])->name('create-claim-form');
    Route::get('/staff/claim-documents', [StaffController::class, 'claimDocuments'])->name('claim-documents');

    // Customers
    Route::get('/staff/customers', [StaffController::class, 'customers'])->name('customers.index');
    Route::get('/staff/customers/{customer}', [StaffController::class, 'showCustomer'])->name('customers.show');

});

// Agent routes — only agents can access
Route::middleware(['agent'])->prefix('agent')->group(function () {
    // mirrors customer claim routes but with ClaimSource::AGENT_PORTAL
    Route::get('/agent/dashboard', [AgentController::class, 'index'])->name('agent.dashboard.index');

    // Claims
    Route::post('/agent/claims', [AgentClaimController::class, 'store'])->name('agent.claims.store');
    Route::get('/agent/claims', [AgentClaimController::class, 'index'])->name('agent.claims.index');
    Route::get('/agent/claims/show/{claim}', [AgentClaimController::class, 'show'])->name('agent.claims.show');
    Route::get('/agent/claims/edit/{claim}', [AgentClaimController::class, 'edit'])->name('agent.claims.edit');
    Route::put('/agent/claims/update/{claim}', [AgentClaimController::class, 'update'])->name('agent.claims.update');

    // Preview Document
    Route::get('/documents/{document}/preview', [AgentClaimController::class, 'previewDocument'])->name('agent.documents.preview');
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

    // Department
    Route::get('/settings/departments', [DepartmentController::class, 'index'])->name('departments.index');
    Route::get('/settings/departments/create', [DepartmentController::class, 'create'])->name('departments.create');
    Route::post('/settings/departments', [DepartmentController::class, 'store'])->name('departments.store');
    Route::get('/settings/departments/{department}/edit', [DepartmentController::class, 'edit'])->name('departments.edit');
    Route::put('/settings/departments/{department}', [DepartmentController::class, 'update'])->name('departments.update');
    Route::delete('/settings/departments/{department}', [DepartmentController::class, 'destroy'])->name('departments.destroy');

    // Staff Management
    Route::get('/settings/create-staff', [StaffController::class, 'create'])->name('staff.create');
    Route::post('/settings/staff-store', [StaffController::class, 'store'])->name('staff.store');
    Route::get('/settings/edit-staff/{staff}', [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('/settings/staff-update/{staff}', [StaffController::class, 'update'])->name('staff.update');
    Route::delete('/settings/staff-delete/{staff}', [StaffController::class, 'destroy'])->name('staff.destroy');

    // Agent Management
    Route::get('/settings/create-agent', [AgentController::class, 'create'])->name('agents.create');
    Route::post('/settings/agents-store', [AgentController::class, 'store'])->name('agents.store');
    Route::get('/settings/edit-agent/{agent}', [AgentController::class, 'edit'])->name('agents.edit');
    Route::put('/settings/agents-update/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('/settings/agents-delete/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
});

// Offline Application
Route::prefix('offline')->name('offline.')->group(function () {
    Route::get('/dashboard', [OfflineController::class, 'index'])->name('dashboard');
    Route::get('/motor-form', [OfflineController::class, 'motorForm'])->name('motor-form');
    Route::get('/general-accident-form', [OfflineController::class, 'generalAccidentForm'])->name('general-accident-form');
    Route::get('/fire-form', [OfflineController::class, 'fireForm'])->name('fire-form');
});
