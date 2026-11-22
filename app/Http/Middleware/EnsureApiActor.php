<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use App\Models\User;
use App\Support\ApiResponse;
use App\Support\StaffAccess;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiActor
{
    public function handle(Request $request, Closure $next, string $actor): Response
    {
        $user = $request->user();

        $allowed = match ($actor) {
            'customer' => $user instanceof Customer && $user->tokenCan('customer'),
            'staff'    => $user instanceof User && $user->tokenCan('staff')
                            && StaffAccess::hasStaffRole($user) && StaffAccess::isActive($user),
            default    => false,
        };

        if (! $allowed) {
            return ApiResponse::error('forbidden', 'This token cannot access this resource.', 403);
        }

        return $next($request);
    }
}