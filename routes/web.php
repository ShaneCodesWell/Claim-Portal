<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\Auth\AgentAuthController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\FireController;
use App\Http\Controllers\GeneralAccidentController;
use App\Http\Controllers\MotorFormController;
// use App\Http\Controllers\OfflineController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\Staff\CommitteeClaimController;
use App\Http\Controllers\Staff\GlimsSyncController;
use App\Http\Controllers\Staff\StaffPolicySearchController;
use App\Http\Controllers\Surveyor\ClaimController as SurveyorClaimController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Agent\ClaimController as AgentClaimController;
use \App\Http\Controllers\Customer\ClaimController as CustomerClaimController;
use \App\Http\Controllers\Staff\ClaimController as StaffClaimController;

// Auth Routes
Route::get('/', [AuthController::class, 'showUserSelectForm'])->name('user.select');

// Customer Auth
Route::get('/login-user', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');

// Staff Auth
Route::get('/staff-login', [AuthController::class, 'staffLoginForm'])->name('staff.login');
Route::post('/staff-login', [AuthController::class, 'staffLogin'])->name('staff.login.submit');

// Agent Auth
Route::get('/agent-login', [AgentAuthController::class, 'showLoginForm'])->name('agent.login');
Route::post('/agent-login/ajax', [AgentAuthController::class, 'loginAjax'])->name('agent.login.ajax');
Route::post('/agent-login/verify-otp', [AgentAuthController::class, 'verifyOtp'])->name('agent.login.verify.otp');
Route::post('/agent-login/resend-otp', [AgentAuthController::class, 'resendOtp'])->name('agent.login.resend.otp');
Route::post('/agent-logout', [AgentAuthController::class, 'logout'])->name('agent.logout');

// AJAX password setup — called from the modal's password setup - multi-step login
Route::post('login/ajax', [AuthController::class, 'loginAjax'])->name('login.ajax');
Route::post('login/select-profile', [AuthController::class, 'selectProfile'])->name('login.select.profile');

Route::post('/login/verify-otp', [AuthController::class, 'verifyOtpAjax'])->name('login.verify.otp');
Route::post('/login/resend-otp', [AuthController::class, 'resendOtp'])->name('login.resend.otp');

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

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/poll', [DashboardController::class, 'pollPolicies'])->name('dashboard.poll')->middleware('auth:customer');

    // Claims
    Route::post('claims', [CustomerClaimController::class, 'store'])->name('claims.store');
    Route::get('claims', [CustomerClaimController::class, 'index'])->name('claims.index');
    Route::get('claims/show/{claim}', [CustomerClaimController::class, 'show'])->name('claims.show');
    Route::get('claims/edit/{claim}', [CustomerClaimController::class, 'edit'])->name('claims.edit');
    Route::put('claims/update/{claim}', [CustomerClaimController::class, 'update'])->name('claims.update');
    Route::post('claims/{claim}/cancel', [CustomerClaimController::class, 'cancel'])->name('claims.cancel');

    // Documents
    Route::post('claims/{claim}/documents', [CustomerClaimController::class, 'uploadDocuments'])->name('customer.claims.documents');
    Route::get('/documents/{document}/preview', [CustomerClaimController::class, 'previewDocument'])->name('customer.documents.preview');
    Route::delete('claims/documents/{document}', [CustomerClaimController::class, 'destroyDocument'])->name('customer.claims.documents.destroy');

    // Save Drafts
    Route::get('claims/drafts', [CustomerClaimController::class, 'drafts'])->name('claims.draft.index');
    Route::post('claims/draft', [CustomerClaimController::class, 'saveDraft'])->name('claims.draft.save');
    Route::get('claims/draft', [CustomerClaimController::class, 'getDraft'])->name('claims.draft.show');
    Route::delete('claims/draft', [CustomerClaimController::class, 'destroyDraft'])->name('claims.draft.destroy');

    Route::get('claims/draft/{draft}/continue', [CustomerClaimController::class, 'continueDraft'])->name('claims.draft.continue');
    Route::delete('claims/draft/{draft}/remove', [CustomerClaimController::class, 'destroyDraftById'])->name('claims.draft.destroyById');

    // Draft Documents
    Route::get('claims/draft/documents/{document}/preview', [CustomerClaimController::class, 'previewDraftDocument'])->name('customer.claims.draft.documents.preview');
    Route::delete('claims/draft/documents/{document}', [CustomerClaimController::class, 'destroyDraftDocument'])->name('customer.claims.draft.documents.destroy');
});

// Staff routes — accessible by ALL staff including admins
Route::middleware(['staff'])->prefix('admin')->group(function () {

    // Claims
    Route::get('claims', [StaffClaimController::class, 'index'])->name('staff.claims.index');
    Route::get('claims/my-queue', [StaffClaimController::class, 'myQueue'])->name('staff.claims.my-queue');
    Route::get('claims/archive', [StaffClaimController::class, 'archive'])->name('staff.claims.archive');
    Route::get('claims/{claim}', [StaffClaimController::class, 'show'])->name('staff.claims.show');

    // Documents
    Route::post('claims/{claim}/documents', [StaffClaimController::class, 'uploadDocuments'])->name('staff.claims.documents');
    Route::get('/documents/{document}/preview', [StaffClaimController::class, 'previewDocument'])->name('staff.documents.preview');
    Route::delete('claims/documents/{document}', [StaffClaimController::class, 'destroyDocument'])->name('staff.claims.documents.destroy');
    Route::get('claims/{claim}/print', [StaffClaimController::class, 'print'])->name('staff.claims.print');

    // Status
    Route::post('claims/{claim}/assign', [StaffClaimController::class, 'assign'])->name('staff.claims.assign');
    Route::post('claims/{claim}/status', [StaffClaimController::class, 'updateStatus'])->name('staff.claims.status');
    Route::post('claims/{claim}/request-info', [StaffClaimController::class, 'requestInfo'])->name('staff.claims.request-info');
    Route::post('claims/{claim}/form-data', [StaffClaimController::class, 'updateFormData'])->name('staff.claims.form-data');
    Route::post('claims/{claim}/finalize', [StaffClaimController::class, 'finalize'])->name('staff.claims.finalize');

    Route::get('claims/{claim}/edit', [StaffClaimController::class, 'edit'])->name('staff.claims.edit');
    Route::put('claims/{claim}/edit', [StaffClaimController::class, 'update'])->name('staff.claims.update');
    Route::post('claims/{claim}/cancel', [StaffClaimController::class, 'cancel'])->name('staff.claims.cancel');

    // Claim Forms & Documents
    Route::get('/staff/claim-forms', [StaffController::class, 'claimForms'])->name('claim-form');

    // Static Views
    Route::get('/staff/claim-forms/view/motor', [StaffController::class, 'claimFormsMotor'])->name('claim-form-motor');
    Route::get('/staff/claim-forms/view/fire', [StaffController::class, 'claimFormsFire'])->name('claim-form-fire');
    Route::get('/staff/claim-forms/view/travel', [StaffController::class, 'claimFormsTravel'])->name('claim-form-travel');

    Route::get('/staff/claim-forms/create', [StaffController::class, 'createClaimForms'])->name('create-claim-form');
    Route::get('/staff/claim-documents', [StaffController::class, 'claimDocuments'])->name('claim-documents');

    // Customers
    Route::get('/staff/customers', [StaffController::class, 'customers'])->name('customers.index');
    Route::get('/staff/customers/{customer}', [StaffController::class, 'showCustomer'])->name('customers.show');

    // Walk-in / staff-initiated policy search
    Route::get('/staff/policy-search', [StaffPolicySearchController::class, 'index'])->name('staff.policy-search.index');
    Route::post('/staff/policy-search', [StaffPolicySearchController::class, 'search'])->name('staff.policy-search.search');

    Route::get('/customers/{customer}/claims/create', [StaffClaimController::class, 'create'])->name('customers.claims.create');
    Route::post('/customers/{customer}/claims', [StaffClaimController::class, 'store'])->name('customers.claims.store');

    // Staff sends a claim to survey or committee
    Route::post('claims/{claim}/send-to-survey', [StaffClaimController::class, 'sendToSurvey'])->name('staff.claims.send-to-survey');
    Route::post('claims/{claim}/send-to-committee', [StaffClaimController::class, 'sendToCommittee'])->name('staff.claims.send-to-committee');
});

// Surveyor portal
Route::middleware(['auth', 'surveyor'])->prefix('surveyor')->name('surveyor.')->group(function () {
    Route::get('claims', [SurveyorClaimController::class, 'index'])->name('claims.index');
    Route::get('claims/my-queue', [SurveyorClaimController::class, 'myQueue'])->name('claims.my-queue');
    Route::get('claims/{claim}', [SurveyorClaimController::class, 'show'])->name('claims.show');
    Route::post('claims/{claim}/complete', [SurveyorClaimController::class, 'complete'])->name('claims.complete');
    Route::post('claims/{claim}/assign-to-me', [SurveyorClaimController::class, 'assignToMe'])->name('claims.assign-to-me');
    Route::post('claims/{claim}/documents', [SurveyorClaimController::class, 'uploadDocuments'])->name('claims.documents');
});

// Claims Committee (staff layout, gated by 'committee' middleware)
Route::middleware(['staff', 'committee'])->prefix('admin/committee')->name('committee.')->group(function () {
    Route::get('claims', [CommitteeClaimController::class, 'index'])->name('claims.index');
    Route::get('claims/{claim}', [CommitteeClaimController::class, 'show'])->name('claims.show');
    Route::post('claims/{claim}/decide', [CommitteeClaimController::class, 'decide'])->name('claims.decide');
    Route::post('claims/{claim}/documents', [CommitteeClaimController::class, 'uploadDocuments'])->name('claims.documents');
});

// Agent routes — only agents can access
Route::middleware(['agent'])->prefix('agent')->group(function () {
    // mirrors customer claim routes but with ClaimSource::AGENT_PORTAL
    Route::get('/agent/dashboard', [AgentController::class, 'index'])->name('agent.dashboard.index');
    Route::get('/agent/search', [AgentController::class, 'search'])->name('agent.policy.search');

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

    // Intermediary Management
    Route::get('/settings/create-agent', [AgentController::class, 'create'])->name('agents.create');
    Route::post('/settings/agents-store', [AgentController::class, 'store'])->name('agents.store');
    Route::get('/settings/edit-agent/{agent}', [AgentController::class, 'edit'])->name('agents.edit');
    Route::put('/settings/agents-update/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::delete('/settings/agents-delete/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
});

// Offline Application
// Route::prefix('offline')->name('offline.')->group(function () {
//     Route::get('/dashboard', [OfflineController::class, 'index'])->name('dashboard');
//     Route::get('/motor-form', [OfflineController::class, 'motorForm'])->name('motor-form');
//     Route::get('/general-accident-form', [OfflineController::class, 'generalAccidentForm'])->name('general-accident-form');
//     Route::get('/fire-form', [OfflineController::class, 'fireForm'])->name('fire-form');
// });
