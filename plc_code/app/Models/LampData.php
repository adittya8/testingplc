<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LampData extends Model
{
    protected $fillable = [
        'brightness',
        'voltage',
        'current',
        'power',
        'work_time',
        'power_cunsumption',
        'pf',
        'luminary_id',
    ];

    public function luminary(): BelongsTo
    {
        return $this->belongsTo(Luminary::class);
    }
}
