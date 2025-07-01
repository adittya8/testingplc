<?php

namespace App\Enums;

enum AlarmTypes: int
{
    // case OVERCURRENT = 1;
    // case UNDERCURRENT = 2;
    case OVERVOLTAGE = 3;
    case UNDERVOLTAGE = 4;

    public function getText(): string
    {
        return match ($this) {
                // self::OVERCURRENT => 'Over Current',
                // self::UNDERCURRENT => 'Under Current',
            self::OVERVOLTAGE => 'Over Voltage',
            self::UNDERVOLTAGE => 'Under Voltage',
        };
    }

    public static function getTextFromValue(int $val): string
    {
        return match (true) {
                // self::OVERCURRENT->value == $val => 'Over Current',
                // self::UNDERCURRENT->value == $val => 'Under Current',
            self::OVERVOLTAGE->value == $val => 'Over Voltage',
            self::UNDERVOLTAGE->value == $val => 'Under Voltage',
        };
    }

    public static function getColorFromValue(int $val): string
    {
        return match (true) {
                // self::OVERCURRENT->value == $val => '#ef4444',
                // self::UNDERCURRENT->value == $val => '#f97316',
            self::OVERVOLTAGE->value == $val => '#494949',
            self::UNDERVOLTAGE->value == $val => '#a8a29e',
        };
    }
}
