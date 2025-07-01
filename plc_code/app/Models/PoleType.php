<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoleType extends Model
{
    protected $fillable = ['name'];

    public function poles(): HasMany
    {
        return $this->hasMany(Pole::class);
    }
}
