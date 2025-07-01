<?php

namespace App\Enums;

enum LuminaryStatuses: int
{
    case ONLINE = 1;
    case OFFLINE = 2;
    case ALARM = 3;

    public function getText(): string
    {
        return match ($this) {
            self::ONLINE => 'Online',
            self::OFFLINE => 'Offline',
            self::ALARM => 'Alarm',
            default => 'Other',
        };
    }

    public static function getTextFromValue(int $val): string
    {
        return match (true) {
            self::ONLINE->value == $val => 'Online',
            self::OFFLINE->value == $val => 'Offline',
            self::ALARM->value == $val => 'Alarm',
            default => 'Other',
        };
    }

    public static function getColorFromValue(int $val): string
    {
        return match (true) {
            self::ONLINE->value == $val => '#10b981',
            self::OFFLINE->value == $val => '#ef4444',
            self::ALARM->value == $val => '#ffc107',
            default => '#a8c5da',
        };
    }
}