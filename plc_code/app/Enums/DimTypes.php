<?php

namespace App\Enums;

enum DimTypes: int
{
    case NON_DIMMING = 1;
    // case STEP_DIMMING = 2;
    // case STEPLESS_DIMMING = 3;

    public function getText(): string
    {
        return match ($this) {
            self::NON_DIMMING => 'Non-dimming',
        // self::STEP_DIMMING => 'Step dimming',
        // self::STEPLESS_DIMMING => 'Stepless dimming',
        };
    }

    public static function getTextFromValue(int $val): string
    {
        return match (true) {
            self::NON_DIMMING->value == $val => 'Non-dimming',
        // self::STEP_DIMMING->value == $val => 'Step dimming',
        // self::STEPLESS_DIMMING->value == $val => 'Stepless dimming',
        };
    }
}