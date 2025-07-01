<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\User as UserModel;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

class User extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $email = '';
    public $mobile = '';
    public $username = '';
    public $password = '';
    public $confirm_password = '';
    public $role_id = '';

    public $user;
    public $editMode = false;
    public $modalTitle = 'Add User';

    public function render()
    {
        return view('livewire.user')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'users' => UserModel::where('project_id', config('project_id'))->paginate(10, ['*'], 'users'),
                'roles' => Role::where('project_id', config('project_id'))
                    ->orWhere('name', 'Project Admin')
                    ->get()
            ]);
    }

    public function storeUser()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $newUser = UserModel::create($validated + [
            'project_id' => config('project_id')
        ]);
        $role = Role::find($validated['role_id']);
        if ($role) {
            $newUser->assignRole($role->name);
        }
        $this->reset();

        $this->dispatch('close-modal', modalId: 'userFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'User has been added.');
    }

    public function editUser(string $id)
    {
        $this->user = UserModel::findOrFail($id);
        $this->name = $this->user->name;
        $this->email = $this->user->email;
        $this->mobile = $this->user->mobile;
        $this->username = $this->user->username;
        $this->role_id = $this->user->roles?->first()?->id;
        $this->editMode = true;
        $this->modalTitle = 'Edit User';
        $this->dispatch('open-modal', modalId: 'userFormModal');
    }

    public function updateUser()
    {
        $validated = $this->validate($this->validationRules(), $this->validationMessages());
        $this->user->update($validated);
        $role = Role::find($validated['role_id']);
        if ($role) {
            $this->user->syncRoles([$role->name]);
        }

        $this->reset();
        $this->editMode = false;
        $this->dispatch('close-modal', modalId: 'userFormModal');
        $this->dispatch('show-toast', type: 'success', message: 'User has been updated.');
        $this->modalTitle = 'Add User';
    }

    #[On('delete-user')]
    public function deleteUser(string $id)
    {
        $user = UserModel::findOrFail($id);
        $user->delete();
        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'User has been deleted.');
    }


    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules(): array
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'mobile' => 'required|string|max:20',
            'username' => 'required|string|max:100|unique:users,username',
            'password' => 'required|string|min:8|max:100',
            'confirm_password' => 'required|string|same:password',
            'role_id' => 'required|exists:roles,id',
        ];

        if ($this->editMode) {
            $rules['email'] = "required|email|max:255|unique:users,email,{$this->user->id}";
            $rules['username'] = "required|string|max:100|unique:users,username,{$this->user->id}'";
            $rules['password'] = "nullable|string|min:8|max:100";
            $rules['confirm_password'] = "nullable|string";
        }

        return $rules;
    }

    private function validationMessages(): array
    {
        return [
            'name.required' => "Please enter user's full name.",
        ];
    }
}
