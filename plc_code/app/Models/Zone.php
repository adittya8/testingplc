<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Zone extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'name',
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

    public function roads(): HasMany
    {
        return $this->hasMany(Road::class);
    }

    public function concentrators(): HasManyThrough
    {
        return $this->hasManyThrough(Concentrator::class, Road::class);
    }
}
