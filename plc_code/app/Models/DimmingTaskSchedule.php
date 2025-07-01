<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DimmingTaskSchedule extends Model
{
    protected $fillable = [
        'dimming_task_id',
        'time',
        'brightness',
    ];

    public function dimmingTask(): BelongsTo
    {
        return $this->belongsTo(DimmingTask::class);
    }
}
