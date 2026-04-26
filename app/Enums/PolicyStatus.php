<?php
namespace App\Enums;

enum PolicyStatus: string
{
    case ACTIVE    = 'active';
    case EXPIRED   = 'expired';
    case CANCELLED = 'cancelled';
    case SUSPENDED = 'suspended';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::ACTIVE->value    => 'Active',
            self::EXPIRED->value   => 'Expired',
            self::CANCELLED->value => 'Cancelled',
            self::SUSPENDED->value => 'Suspended',
        ];
    }
}