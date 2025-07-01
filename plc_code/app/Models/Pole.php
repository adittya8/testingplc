<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pole extends Model
{
    use LogsActivity;

    protected $fillable = [
        'code',
        'road_id',
        'concentrator_id',
        'pole_type_id',
        'project_id',
        'lat',
        'lon',
        'location',
        'serial',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function road(): BelongsTo
    {
        return $this->belongsTo(Road::class);
    }

    public function concentrator(): BelongsTo
    {
        return $this->belongsTo(Concentrator::class);
    }

    public function poleType(): BelongsTo
    {
        return $this->belongsTo(PoleType::class);
    }

    public function luminaries(): HasMany
    {
        return $this->hasMany(Luminary::class);
    }

    public function rtus(): HasMany
    {
        return $this->hasMany(RemoteTerminal::class);
    }
}
