<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

class RolePermission extends Component
{
    public $role;
    public $permissionIds;
    public $allPermissions;
    public $checkAll = false;

    public function mount(Role $role)
    {
        $this->role = $role;
        $this->permissionIds = $role->permissions->pluck('id')->toArray();
    }

    public function render()
    {
        // $userPermissions = DB::table('model_has_permissions as MHP')
        //     ->join('users as U', 'MHP.model_id', '=', 'U.id')
        //     ->join('projects as P', 'MHP.project_id', '=', 'P.id')
        //     ->select('MHP.*')
        //     ->get();
        // $allPermissions = Permission::all();
        // $projectsWithPermissions = $this->projects->whereIn('id', $userPermissions->pluck('project_id'));
        // $this->permissions = [];
        // $this->projectsForSelect = Project::all();
        // foreach ($projectsWithPermissions as $pwp) {
        //     $projectPermissions = [];
        //     foreach ($allPermissions as $ap) {
        //         $isPermitted = $userPermissions->where('permission_id', $ap->id)->where('project_id', $pwp->id)->first();
        //         $projectPermissions[] = [
        //             'permission_id' => $ap->id,
        //             'name' => $ap->name,
        //             'has_permission' => $isPermitted ? true : false,
        //         ];
        //     }
        //     $this->permissions[$pwp->id] = $projectPermissions;
        //     $this->projectsForSelect->forget($this->projectsForSelect->search(fn($i) => $i->id == $pwp->id));
        // }

        $this->allPermissions = PermissionModel::where('type', 'project')->select('id', 'name', 'group_id')->get();

        return view('livewire.role-permission')
            ->extends('layouts.layout')
            ->section('content');
    }

    public function rolePermissions()
    {
        $permissions = DB::table('permissions')->whereIn('id', $this->permissionIds)->pluck('name');
        $this->role->syncPermissions($permissions);
        $this->dispatch('show-toast', type: 'success', message: 'Permissions has been updated.');
    }

    #[On('check-all')]
    public function checkAll($check)
    {
        if ($check == 1) {
            $this->permissionIds = $this->allPermissions->pluck('id')->toArray();
        } else {
            $this->permissionIds = $this->role->permissions->pluck('id')->toArray();
        }
    }
}
