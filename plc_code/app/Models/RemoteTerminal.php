<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class RemoteTerminal extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'code',
        'concentrator_id',
        'project_id',
        'pole_id',
        'sub_group_id',
        'brand_id',
        'lat',
        'lon',
        'location',
        'remarks',
        'rated_power',
        'status_updated_at',
        'last_command_brightness',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function concentrator(): BelongsTo
    {
        return $this->belongsTo(Concentrator::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function subGroup(): BelongsTo
    {
        return $this->belongsTo(SubGroup::class);
    }

    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    public function reportData(): HasMany
    {
        return $this->hasMany(ReportData::class, 'rtu_code', 'code');
    }

    public function lastReportData(): HasOne
    {
        return $this->hasOne(ReportData::class, 'rtu_code', 'code')->latestOfMany();
    }

    public function commands(): MorphMany
    {
        return $this->morphMany(Command::class, 'commandable');
    }

    public function lastCommand(): MorphOne
    {
        return $this->morphOne(Command::class, 'commandable')->latestOfMany();
    }

    public function luminary(): HasOne
    {
        return $this->hasOne(Command::class, 'rtu_id', 'id')->oldestOfMany();
    }

    public function runningTimes(): HasMany
    {
        return $this->hasMany(DailyRunningTime::class, 'rtu_code', 'code');
    }
}
