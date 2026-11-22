<?php

namespace App\Support;

use App\Enums\ClaimStatus;
use App\Enums\UserRole;
use App\Models\Claim;
use App\Models\User;

class StaffAccess
{
    private static function role(User $user): string
    {
        return mb_strtolower((string) $user->role);
    }

    /** May log in to the staff API (every staff role). */
    public static function hasStaffRole(User $user): bool
    {
        return (bool) $user->is_admin || in_array(self::role($user), UserRole::staffRoles(), true);
    }

    public static function isActive(User $user): bool
    {
        return ! isset($user->is_active) || (bool) $user->is_active;
    }

    /** May change claims (process, edit, route, finalize). Surveyors and reviewers only read. */
    public static function canHandle(User $user): bool
    {
        return self::isActive($user)
            && ((bool) $user->is_admin || in_array(self::role($user), config('api.handler_roles'), true));
    }

    /** Handler role, and either the assignee, a claim head or an admin. */
    public static function canActOn(User $user, Claim $claim): bool
    {
        return self::canHandle($user)
            && ($user->isAdmin() || $user->isClaimHead() || $claim->assigned_to === $user->id);
    }

    /** Statuses this person may cancel from. Empty when they may not cancel at all. */
    public static function cancellable(User $user, Claim $claim): array
    {
        if (! self::canActOn($user, $claim)) {
            return [];
        }

        return $user->isAdmin()
            ? array_merge(ClaimStatus::staffCancellable(), ClaimStatus::adminCancellable())
            : ClaimStatus::staffCancellable();
    }
}
