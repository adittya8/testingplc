<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DimmingTask extends Model
{
    protected $fillable = [
        'name',
        'dimming_task_category_id',
        'project_id',
        'date_from',
        'date_to',
        'is_active',
        'type', //1 => subgroup, 2 => rtu
        'last_command_sent_at',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(DimmingTaskCategory::class, 'dimming_task_category_id', 'id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(DimmingTaskGroup::class, 'dimming_task_id', 'id');
    }

    // public function schedules(): BelongsToMany
    // {
    //     return $this->belongsToMany(DimmingSchedule::class, 'dimming_task_schedules', 'dimming_task_id', 'dimming_schedule_id')
    //         ->withPivot(['sub_group_id', 'luminary_id']);
    // }

    public function schedules(): HasMany
    {
        return $this->hasMany(DimmingTaskSchedule::class);
    }

    public function weekdays(): HasMany
    {
        return $this->hasMany(DimmingTaskWeekday::class);
    }
}
