<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DimmingSchedule extends Model
{
    protected $fillable = [
        'name',
        'road_id',
        'dimming_type',
        'start_time',
        'end_time',
        'status',
    ];

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(DimmingTask::class, 'dimming_task_schedules', 'dimming_schedule_id', 'dimming_task_id');
    }
}
