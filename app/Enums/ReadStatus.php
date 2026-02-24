<?php

namespace App\Enums;

enum ReadStatus: int
{
    case READ = 1;
    case READING = 2;
    case TO_READ = 3;

    public static function toArray(): array
    {
        return array_column(self::cases(), 'value', 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
