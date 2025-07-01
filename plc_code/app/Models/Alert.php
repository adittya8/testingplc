<?php

namespace App\Models;

use App\Enums\AlarmTypes;
use Illuminate\Database\Eloquent\Model;

class Alert extends Model
{
    protected $fillable = [
        'dcu_code',
        'rtu_code',
        'alert_type',
        'alert_details',
        'device_time',
    ];

    protected function casts(): array
    {
        return [
            'alert_type' => AlarmTypes::class,
        ];
    }

    public function dcu()
    {
        return $this->belongsTo(Concentrator::class, 'dcu_code', 'concentrator_no');
    }

    public function rtu()
    {
        return $this->belongsTo(RemoteTerminal::class, 'rtu_code', 'code');
    }
}
