<?php

namespace App\Enums;

enum PolicySource : string
{
    case MANUAL = 'manual';
    case GENOVA = 'genova';
    case GLIMS  = 'glims';

    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function labels(): array
    {
        return [
            self::MANUAL->value => 'Manual',
            self::GENOVA->value => 'Genova Insure',
            self::GLIMS->value  => 'GLIMS',
        ];
    }
}
