<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Concentrator extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'project_id',
        'road_id',
        'concentrator_no',
        'sim_no',
        'lat',
        'lon',
        'location',
        'last_status',
        'status_updated_at',
        'remarks',
        'schedule_preset_id',
        'synced_at',
        'synced_by',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function poles(): HasMany
    {
        return $this->hasMany(Pole::class);
    }

    public function commands(): MorphMany
    {
        return $this->morphMany(Command::class, 'commandable');
    }

    public function lastCommand(): MorphOne
    {
        return $this->morphOne(Command::class, 'commandable')->latestOfMany();
    }

    public function schedulePreset(): BelongsTo
    {
        return $this->belongsTo(SchedulePreset::class);
    }
}
