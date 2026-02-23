<?php

namespace App\Enums;

enum BookStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
