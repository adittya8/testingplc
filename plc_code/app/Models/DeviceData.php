<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceData extends Model
{
    protected $fillable = [
        'device_code',
        'data',
        'topic',
    ];
}
