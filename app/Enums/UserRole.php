<?php
namespace App\Enums;

enum UserRole: string {
    case ADMIN      = 'admin';
    case CLAIM_HEAD = 'claim_head';
    case CLAIMS_ADJUSTER = 'claims_adjuster';
    case SURVEYOR = 'surveyor';
    case REVIEWER   = 'reviewer';
    case STAFF      = 'staff';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function staffRoles(): array
    {
        return [
            self::ADMIN->value,
            self::CLAIM_HEAD->value,
            self::CLAIMS_ADJUSTER->value,
            self::SURVEYOR->value,
            self::REVIEWER->value,
            self::STAFF->value,
        ];
    }

    public static function labels(): array
    {
        return [
            self::ADMIN->value      => 'Administrator',
            self::CLAIM_HEAD->value => 'Claim Head',
            self::CLAIMS_ADJUSTER->value => 'Claims Adjuster',
            self::SURVEYOR->value   => 'Surveyor',
            self::REVIEWER->value   => 'Reviewer',
            self::STAFF->value      => 'Staff',
        ];
    }
}
