<?php

namespace App\Enums;

enum SocialiteProviders: int
{
    case GOOGLE = 1;
    case APPLE = 2;

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
