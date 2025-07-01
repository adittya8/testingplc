<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class LuminaryType extends Model
{
    use LogsActivity;

    protected $fillable = [
        'model',
        'brand_id',
        'light_source_type_id',
        'rated_power',
        'avg_life',
        'remarks',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function lightSourceType(): BelongsTo
    {
        return $this->belongsTo(LightSourceType::class);
    }
}
