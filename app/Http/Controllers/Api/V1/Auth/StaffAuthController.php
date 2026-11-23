<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\StaffResource;
use App\Models\User;
use App\Support\ApiResponse;
use App\Support\StaffAccess;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Dedoc\Scramble\Attributes\Group;

#[Group('Authentication', weight: 1)]
class StaffAuthController extends Controller
{
    /**
     * Staff login
     *
     * @unauthenticated
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $email = mb_strtolower($credentials['email']);
        $key   = 'api-staff-login:' . $email . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            return ApiResponse::error(
                'too_many_attempts',
                'Too many login attempts. Try again in ' . RateLimiter::availableIn($key) . ' seconds.',
                429
            );
        }

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            RateLimiter::hit($key, 300);
            return ApiResponse::error('invalid_credentials', 'These credentials do not match our records.', 401);
        }

        if (! StaffAccess::hasStaffRole($user)) {
            RateLimiter::hit($key, 300);
            return ApiResponse::error('not_staff', 'You do not have staff access.', 403);
        }

        if (! StaffAccess::isActive($user)) {
            return ApiResponse::error('account_inactive', 'This account is inactive.', 403);
        }

        RateLimiter::clear($key);

        $expires = now()->addMinutes(config('api.staff_token_minutes'));
        $token   = $user->createToken('api-staff', ['staff'], $expires);

        return response()->json(['data' => [
            'token_type'   => 'Bearer',
            'access_token' => $token->plainTextToken,
            'expires_at'   => $expires->toIso8601String(),
            'staff'        => new StaffResource($user),
        ]]);
    }
}
