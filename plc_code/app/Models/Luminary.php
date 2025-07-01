<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Luminary extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'node_id',
        'lamp_type_id',
        'concentrator_id',
        'rtu_id',
        'sub_group_id',
        'luminary_type_id',
        'control_gear_type_id',
        'pole_id',
        'installation_status',
        'rated_power',
        'remarks',
        'last_status',
        'status_updated_at',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()->logAll();
    }

    public function concentrator(): BelongsTo
    {
        return $this->belongsTo(Concentrator::class);
    }

    public function rtu(): BelongsTo
    {
        return $this->belongsTo(RemoteTerminal::class, 'rtu_id', 'id');
    }

    public function lampType(): BelongsTo
    {
        return $this->belongsTo(LampType::class);
    }

    public function pole(): BelongsTo
    {
        return $this->belongsTo(Pole::class);
    }

    public function subGroup(): BelongsTo
    {
        return $this->belongsTo(SubGroup::class);
    }

    public function luminaryType(): BelongsTo
    {
        return $this->belongsTo(LuminaryType::class);
    }

    public function controlGearType(): BelongsTo
    {
        return $this->belongsTo(ControlGearType::class);
    }

    public function lampData()
    {
        return $this->hasOne(LampData::class)->latestOfMany('created_at');
    }
}
