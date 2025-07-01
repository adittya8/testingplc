<?php

namespace App\Enums;

enum CommandTypes: int
{
    case DCU_DIMMING = 1;
    case RTU_DIMMING = 2;
    case DCU_SINGLE_SCHEDULE = 3;
    case RTU_SINGLE_SCHEDULE = 4;
    case METHOD_SET_SCHEDULE = 5;
    case METHOD_UNSET_SCHEDULE = 6;
    case GROUP_DIMMING = 7;
    case SUB_GROUP_DIMMING = 8;
    case GROUP_SCHEDULE = 9;
    case SUB_GROUP_SCHEDULE = 10;

    public function getText(): string
    {
        return match ($this) {
            self::DCU_DIMMING => 'DCU Dimming',
            self::RTU_DIMMING => 'RTU Dimming',
            self::DCU_SINGLE_SCHEDULE => 'DCU Single Schedule',
            self::RTU_SINGLE_SCHEDULE => 'RTU Single Schedule',
            self::METHOD_SET_SCHEDULE => 'Method Set Schedule',
            self::METHOD_UNSET_SCHEDULE => 'Method Unset Schedule',
            self::GROUP_DIMMING => 'Group Dimming',
            self::SUB_GROUP_DIMMING => 'Sub Group Dimming',
            self::GROUP_SCHEDULE => 'Group Schedule',
            self::SUB_GROUP_SCHEDULE => 'Sub Group Schedule',
        };
    }

    public static function getTextFromValue(int $val): string
    {
        return match (true) {
            self::DCU_DIMMING->value == $val => 'DCU Dimming',
            self::RTU_DIMMING->value == $val => 'RTU Dimming',
            self::DCU_SINGLE_SCHEDULE->value == $val => 'DCU Single Schedule',
            self::RTU_SINGLE_SCHEDULE->value == $val => 'RTU Single Schedule',
            self::METHOD_SET_SCHEDULE->value == $val => 'Method Set Schedule',
            self::METHOD_UNSET_SCHEDULE->value == $val => 'Method Unset Schedule',
            self::GROUP_DIMMING->value == $val => 'Group Dimming',
            self::SUB_GROUP_DIMMING->value == $val => 'Sub Group Dimming',
            self::GROUP_SCHEDULE->value == $val => 'Group Schedule',
            self::SUB_GROUP_SCHEDULE->value == $val => 'Sub Group Schedule',
        };
    }
}
