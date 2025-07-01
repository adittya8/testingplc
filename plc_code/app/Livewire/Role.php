<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role as RoleModel;

class Role extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';

    public $role;
    public $editMode = false;
    public $modalTitle = 'Add Role';

    public function render()
    {
        return view('livewire.role')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'roles' => RoleModel::where('project_id', config('project_id'))
                    ->orWhere('name', 'Project Admin')
                    ->paginate(20, ['*'], 'roles'),
            ]);
    }

    public function storeRole()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        RoleModel::create($validated + [
            'project_id' => config('project_id')
        ]);
        $this->reset();

        $this->dispatch('close-modal', modalId: 'roleFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Role has been added.');
    }

    public function editRole(string $id)
    {
        $this->role = RoleModel::findOrFail($id);
        $this->name = $this->role->name;
        $this->editMode = true;
        $this->modalTitle = 'Edit Role';
        $this->dispatch('open-modal', modalId: 'roleFormModal');
    }

    public function updateRole()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->role->update($validated);

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'roleFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'Role has been updated.');
        $this->modalTitle = 'Add Role';
    }

    #[On('delete-role')]
    public function deleteRole(string $id)
    {
        $role = RoleModel::findOrFail($id);
        if ($role->users && count($role->users)) {
            $this->dispatch('show-toast', type: 'danger', message: 'This role is in use by one or more users.');
            return;
        }

        $role->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Role has been deleted.');
    }


    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules(): array
    {
        $rules = [
            'name' => 'required|string|max:255|unique:roles,name',
        ];

        if ($this->editMode) {
            $rules['name'] = "required|string|max:255|unique:roles,name,{$this->role->id}";
        }

        return $rules;
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => 'Please enter role name.',
        ];
    }
}
