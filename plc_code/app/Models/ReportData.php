<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportData extends Model
{
    protected $fillable = [
        'rtu_code',
        'dcu_code',
        'voltage',
        'main_light_current',
        'main_light_power',
        'temperature',
        'main_light_brightness',
        'main_light_color_temp',
        'running_time',
        'running_mode',
        'total_power_consumption',
        'device_time',
        'raw_data',
    ];
}
