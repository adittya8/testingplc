<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SchedulePreset extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'project_id',
        'schedule',
    ];

    protected function casts(): array
    {
        return [
            'schedule' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }
}
