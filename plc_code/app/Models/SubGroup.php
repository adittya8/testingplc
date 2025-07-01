<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SubGroup extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'group_id',
        'project_id',
        'remarks',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
