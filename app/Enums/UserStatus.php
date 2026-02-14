<?php

namespace App\Enums;

enum UserStatus: int
{
    case ACTIVE = 1;
    case INACTIVE = 2;
    case DELETED = 3;

    // çıktı: ['Active' => 1, 'Inactive' => 2, 'Deleted' => 3]
    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    // çıktı: [1, 2, 3]
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
