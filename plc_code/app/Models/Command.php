<?php

namespace App\Models;

use App\Enums\CommandTypes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Command extends Model
{
    protected $fillable = [
        'command',
        'concentrator_id',
        'rtu_id',
        'commandable_type',
        'commandable_id',
        'command_type',
        'is_sent',
    ];

    protected function casts(): array
    {
        return [
            'command_type' => CommandTypes::class,
        ];
    }

    public function concentrator(): BelongsTo
    {
        return $this->belongsTo(Concentrator::class);
    }

    public function rtu(): BelongsTo
    {
        return $this->belongsTo(RemoteTerminal::class, 'rtu_id', 'id');
    }

    public function commandable(): MorphTo
    {
        return $this->morphTo();
    }
}
