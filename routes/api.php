<?php

use App\Http\Controllers\Api\V1\Customer\ClaimController as ApiClaimController;
use App\Http\Controllers\Api\V1\Customer\ClaimFormController;
use App\Http\Controllers\Api\V1\Customer\DocumentController;
use App\Http\Controllers\Api\V1\Customer\PolicyController;
use App\Http\Controllers\Api\V1\Auth\CustomerAuthController;
use App\Http\Controllers\Api\V1\Auth\StaffAuthController;
use App\Http\Controllers\Api\V1\Auth\TokenController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::middleware('throttle:api-auth')->group(function () {
            Route::post('customer/request-otp', [CustomerAuthController::class, 'requestOtp']);
            Route::post('customer/verify-otp', [CustomerAuthController::class, 'verifyOtp']);
            Route::post('customer/select-profile', [CustomerAuthController::class, 'selectProfile']);
            Route::post('staff/login', [StaffAuthController::class, 'login']);
        });

        Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
            Route::get('me', [TokenController::class, 'me']);
            Route::post('logout', [TokenController::class, 'logout']);
        });
    });

    Route::middleware(['auth:sanctum', 'api.actor:customer', 'throttle:api'])->group(function () {
        Route::get('policies', [PolicyController::class, 'index']);
        Route::get('policies/{policy}', [PolicyController::class, 'show'])->whereNumber('policy');

        Route::get('claim-forms', [ClaimFormController::class, 'index']);
        Route::get('claim-forms/{type}', [ClaimFormController::class, 'show']);

        Route::get('claims', [ApiClaimController::class, 'index']);
        Route::get('claims/{claim}', [ApiClaimController::class, 'show'])->whereNumber('claim')->name('api.v1.claims.show');
        Route::get('documents/{document}', [DocumentController::class, 'show'])->whereNumber('document')->name('api.v1.documents.show');

        Route::middleware('throttle:api-writes')->group(function () {
            Route::post('claims', [ApiClaimController::class, 'store'])->name('api.v1.claims.store');
            Route::post('claims/{claim}/documents', [ApiClaimController::class, 'uploadDocuments'])->whereNumber('claim');
            Route::post('claims/{claim}/respond-info', [ApiClaimController::class, 'respondInfo'])->whereNumber('claim');
            Route::post('claims/{claim}/cancel', [ApiClaimController::class, 'cancel'])->whereNumber('claim');
        });
    });

    // Phase 4: ->middleware(['auth:sanctum', 'api.actor:staff', 'throttle:api'])
});
