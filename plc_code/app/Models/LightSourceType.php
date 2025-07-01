<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LightSourceType extends Model
{
    protected $fillable = [
        'name'
    ];

    public function luminaries(): HasMany
    {
        return $this->hasMany(Luminary::class);
    }
}
