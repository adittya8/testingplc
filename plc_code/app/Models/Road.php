<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Road extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'name',
        'zone_id',
        'grade',
        'length',
        'remarks',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function dcus(): HasMany
    {
        return $this->hasMany(Concentrator::class);
    }

    public function poles(): HasMany
    {
        return $this->hasMany(Pole::class);
    }

    public function luminaries(): HasManyThrough
    {
        return $this->hasManyThrough(Luminary::class, Pole::class);
    }
}
