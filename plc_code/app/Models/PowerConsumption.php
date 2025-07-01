<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PowerConsumption extends Model
{
    protected $fillable = [
        'dcu_code',
        'rtu_code',
        'power',
        'device_time',
    ];

    public function dcu(): BelongsTo
    {
        return $this->belongsTo(Concentrator::class, 'dcu_code', 'concentrator_no');
    }

    public function rtu(): BelongsTo
    {
        return $this->belongsTo(RemoteTerminal::class, 'rtu_code', 'code');
    }
}
