<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Group extends Model
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

    public function subGroups(): HasMany
    {
        return $this->hasMany(SubGroup::class);
    }

    public function rtus(): HasManyThrough
    {
        return $this->hasManyThrough(RemoteTerminal::class, SubGroup::class);
    }

    public function luminaries(): HasManyThrough
    {
        return $this->hasManyThrough(Luminary::class, SubGroup::class);
    }
}
