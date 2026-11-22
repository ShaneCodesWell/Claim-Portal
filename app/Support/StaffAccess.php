<?php

namespace App\Support;

use App\Models\User;

class StaffAccess
{
    public static function hasStaffRole(User $user): bool
    {
        return (bool) $user->is_admin
            || in_array(mb_strtolower((string) $user->role), config('api.staff_roles'), true);
    }

    public static function isActive(User $user): bool
    {
        return ! isset($user->is_active) || (bool) $user->is_active;
    }
}