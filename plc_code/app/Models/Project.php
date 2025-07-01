<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Project extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'image',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    // public function users(): BelongsToMany
    // {
    //     return $this->belongsToMany(User::class, 'user_project', 'project_id', 'user_id');
    // }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function roads(): HasManyThrough
    {
        return $this->hasManyThrough(Road::class, Zone::class);
    }
}
