<?php

namespace App\Enums;

enum LampTypes: int
{
    case SINGLECOLORTEMP = 1;
    case DOUBLECOLORTEMP = 1;

    public function getText(): string
    {
        return match ($this) {
            self::SINGLECOLORTEMP => 'Single Color Temparature',
            self::DOUBLECOLORTEMP => 'Double Color Temparature',
        };
    }

    public static function getTextFromValue(int $val): string
    {
        return match (true) {
            self::SINGLECOLORTEMP->value == $val => 'Single Color Temparature',
            self::DOUBLECOLORTEMP->value == $val => 'Double Color Temparature',
        };
    }
}