<?php

namespace App\Enums;

enum SocialiteProviders: string
{
    case GOOGLE = 'google';
    case APPLE = 'apple';

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
