<?php
namespace App\Enums;

enum UserRole: string {
    case ADMIN      = 'admin';
    case BROKER     = 'broker';
    case CLAIM_HEAD = 'claim_head';
    case CLAIMS_ADJUSTER = 'claims_adjuster';
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
            self::BROKER->value,
            self::CLAIM_HEAD->value,
            self::CLAIMS_ADJUSTER->value,
            self::REVIEWER->value,
            self::STAFF->value,
        ];
    }

    public static function labels(): array
    {
        return [
            self::ADMIN->value      => 'Administrator',
            self::BROKER->value     => 'Broker',
            self::CLAIM_HEAD->value => 'Claim Head',
            self::CLAIMS_ADJUSTER->value => 'Claims Adjuster',
            self::REVIEWER->value   => 'Reviewer',
            self::STAFF->value      => 'Staff',
        ];
    }
}
