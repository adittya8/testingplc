<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ControlGear extends Model
{
    protected $fillable = [
        'model',
        'brand_id',
        'light_source_type_id',
        'control_gear_type_id',
        'dimming_type',
        'dimming_attribute',
        'main_road_standard_voltage',
        'main_road_standard_current',
        'remarks',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function lightSourceType(): BelongsTo
    {
        return $this->belongsTo(LightSourceType::class);
    }

    public function controlGearType(): BelongsTo
    {
        return $this->belongsTo(ControlGearType::class);
    }
}
