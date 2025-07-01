<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DimmingTaskGroup extends Model
{
    protected $fillable = [
        'dimming_task_id',
        'sub_group_id',
        'rtu_id',
        'luminary_no',
        'concentrator_no',
        'dimming_schedule_id',
    ];

    public function dimmingTask(): BelongsTo
    {
        return $this->belongsTo(DimmingTask::class);
    }

    public function subGroup(): BelongsTo
    {
        return $this->belongsTo(SubGroup::class);
    }

    public function rtu(): BelongsTo
    {
        return $this->belongsTo(RemoteTerminal::class, 'rtu_id', 'id');
    }
}
