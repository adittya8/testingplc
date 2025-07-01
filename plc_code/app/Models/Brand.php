<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Brand extends Model
{
    use LogsActivity;

    protected $fillable = [
        'project_id',
        'name',
        'type',
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

    public function luminaryTypes(): HasMany
    {
        return $this->hasMany(LuminaryType::class);
    }
}
