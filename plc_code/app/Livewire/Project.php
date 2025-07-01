<?php

namespace App\Livewire;

use App\Models\BasePermission;
use App\Models\Permission;
use App\Models\Project as ProjectModel;
use App\Services\FileService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Throwable;

class Project extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';

    public $name = '';
    public $image = '';
    public $project;
    public $editMode = false;
    public $modalTitle = 'Add New Project';

    public function render()
    {
        $concentratorsCount = DB::table('projects as P')
            ->join('zones as Z', 'P.id', '=', 'Z.project_id')
            ->join('roads as RD', 'Z.id', '=', 'RD.zone_id')
            ->join('concentrators as C', 'RD.id', '=', 'C.road_id')
            ->select('P.id', DB::raw("count(C.id) as concentrators_count"))
            ->groupBy('P.id')
            ->get();
        $luminariesCount = DB::table('projects as P')
            ->join('zones as Z', 'P.id', '=', 'Z.project_id')
            ->join('roads as RD', 'Z.id', '=', 'RD.zone_id')
            ->join('concentrators as C', 'RD.id', '=', 'C.road_id')
            ->join('luminaries as L', 'C.id', '=', 'L.concentrator_id')
            ->select('P.id', DB::raw("count(L.id) as luminaries_count"))
            ->groupBy('P.id')
            ->get();

        $projects = ProjectModel::get();
        $projects = $projects->map(function ($q) use ($concentratorsCount, $luminariesCount) {
            $cCount = $concentratorsCount->where('id', $q->id)->first();
            $q->concentrators_count = $cCount->concentrators_count ?? 0;

            $lCount = $luminariesCount->where('id', $q->id)->first();
            $q->luminaries_count = $lCount->luminaries_count ?? 0;

            return $q;
        });

        return view('livewire.project')
            ->extends('layouts.layout')
            ->section('content')
            ->with([
                'projects' => $projects,
            ]);
    }

    public function storeProject()
    {
        DB::beginTransaction();
        try {
            $validated = $this->validate($this->validationRules(), $this->validationMessages());
            $project = ProjectModel::create(array_map(fn($v) => $v === '' ? null : $v, $validated));
            if (isset($validated['image']) && $validated['image']) {
                $fileName = "{$project->id}-" . date('YmdHis') . '-' . Str::uuid()->toString() . '-' . rand(10000, 99999);
                $project->image = FileService::uploadFile($validated['image'], $project, $fileName);
                $project->save();
            }

            $basePermissions = BasePermission::all();
            $permissions = [];
            foreach ($basePermissions as $bp) {
                $permissions[] = [
                    'name' => "{$project->id}-{$bp->name}",
                    'base_permission_id' => $bp->id,
                    'guard_name' => 'web',
                ];
            }
            Permission::insert($permissions);

            DB::commit();

            $this->reset();
            $this->dispatch('close-modal', modalId: 'projectFormModal');
            $this->dispatch('show-toast', type: 'success', message: 'Project has been added.');
        } catch (Throwable $t) {
            DB::rollback();
            dd($t);
            $this->addError('submit_error', 'Something went wrong!');
        }
    }

    public function editProject(string $id)
    {
        $this->project = ProjectModel::findOrFail($id);
        $this->name = $this->project->name;
        $this->image = $this->project->image;

        $this->editMode = true;
        $this->modalTitle = 'Edit Project';
        $this->dispatch('open-modal', modalId: 'projectFormModal');
    }

    public function updateProject()
    {
        DB::beginTransaction();
        try {
            $validated = $this->validate($this->validationRules(), $this->validationMessages());
            $this->project->update(array_map(function ($value) {
                return $value === '' ? null : $value;
            }, $validated));

            if (isset($validated['image'])) {
                if ($this->project->image) {
                    FileService::deleteFile(getStoragePath($this->project) . 'image');
                }
                $fileName = "{$this->project->id}-" . date('YmdHis') . '-' . Str::uuid()->toString() . '-' . rand(10000, 99999);
                $this->project->image = FileService::uploadFile($validated['image'], $this->project, $fileName);
                $this->project->save();
            }
            DB::commit();

            $this->reset();
            $this->editMode = false;
            $this->dispatch('close-modal', modalId: 'projectFormModal');
            $this->dispatch('show-toast', type: 'success', message: 'Project has been updated.');
            $this->modalTitle = 'Add Project';
        } catch (Throwable $t) {
            DB::rollback();
            $this->addError('submit_error', 'Something went wrong!');
        }
    }

    #[On('delete-project')]
    public function deleteProject(string $id)
    {
        $project = ProjectModel::findOrFail($id);
        $project->delete();
        if ($project->image) {
            FileService::deleteFile(getStoragePath($project) . 'image');
        }

        $this->dispatch('hide-loader');
        $this->dispatch('show-toast', type: 'success', message: 'Project has been deleted.');
    }


    #[On('reset-form')]
    public function resetForm()
    {
        $this->reset();
    }

    private function validationRules()
    {
        return [
            'name' => 'required|string|max:255',
            'image' => 'nullable|mimes:jpg,png,webp|max:1024',
        ];
    }

    private function validationMessages()
    {
        return [
            'name.required' => 'Please enter project name.',
            'image.mimes' => 'Invalid file type. Allowed types are: jpg, png and webp.',
        ];
    }
}
