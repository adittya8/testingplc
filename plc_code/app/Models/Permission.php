<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Models\Permission as SpatiePermission;
use Spatie\Permission\PermissionRegistrar;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
    ];

    // public function basePermission(): BelongsTo
    // {
    //     return $this->belongsTo(BasePermission::class);
    // }

    public function users(): BelongsToMany
    {
        return $this->morphedByMany(
            getModelForGuard($this->attributes['guard_name'] ?? config('auth.defaults.guard')),
            'model',
            config('permission.table_names.model_has_permissions'),
            app(PermissionRegistrar::class)->pivotPermission,
            config('permission.column_names.model_morph_key')
        )->withPivot('project_id');
    }
}
