<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DimmingTask;
use App\Models\DimmingTaskGroup;
use App\Models\DimmingTaskSchedule;
use App\Models\RemoteTerminal;
use App\Models\SubGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class DimmingTaskController extends Controller
{
    public function addRtus(string $projectId, string $taskId)
    {
        $task = DimmingTask::where('project_id', $projectId)
            ->where('id', $taskId)
            ->with('groups.subGroup', 'groups.rtu')
            ->firstOrFail();

        if ($task->groups->first()?->sub_group_id) {
            $allSubGroups = SubGroup::where('project_id', config('project_id'))->with('group')->withCount('rtus')->get();
        } else if ($task->groups->first()?->rtu_id) {
            $allRtus = RemoteTerminal::where('project_id', config('project_id'))->with('concentrator')->get();
        }

        return view('web.dimming-tasks.add-rtu', [
            'task' => $task,
            'allRtus' => $allRtus ?? null,
            'allSubGroups' => $allSubGroups ?? null,
        ]);
    }

    public function storeRtus(Request $request, string $projectId, string $taskId)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:sg,ind',
            'rtu_ids' => 'nullable|required_if:type,ind|array|min:1',
            'rtu_ids.*' => 'exists:remote_terminals,id',
            'sg_ids' => 'nullable|required_if:type,sg|array|min:1',
            'sg_ids.*' => 'exists:sub_groups,id',
        ], [
            'type.required' => 'Please select a type.',
            'type.in' => 'Invalid type.',
            'rtu_ids.required_if' => 'Please select at least 1 RTU.',
            'rtu_ids.array' => 'Please select at least 1 RTU.',
            'rtu_ids.min' => 'Please select at least 1 RTU.',
            'sg_ids.required_if' => 'Please select at least 1 Sub-Group.',
            'sg_ids.array' => 'Please select at least 1 Sub-Group.',
            'sg_ids.min' => 'Please select at least 1 Sub-Group.',
        ]);

        if ($validator->fails()) {
            return back()->withInput($request->only('type'))->withErrors($validator->getMessageBag());
        }
        $task = DimmingTask::findOrFail($taskId);

        DB::beginTransaction();
        try {
            if ($request->type == 'ind') {
                $rtus = RemoteTerminal::whereIn('id', $request->rtu_ids)->where('project_id', $projectId)->get();
                $now = date('Y-m-d H:i:s');
                $data = [];
                foreach ($rtus as $rtu) {
                    $data[] = [
                        'dimming_task_id' => $taskId,
                        'rtu_id' => $rtu->id,
                        'created_at' => $now
                    ];
                }
                $task->update(['type' => 2]);
                DB::table('dimming_task_groups')->where('dimming_task_id', $taskId)->delete();
                DimmingTaskGroup::insert($data);
            } else if ($request->type == 'sg') {
                $sgs = SubGroup::whereIn('id', $request->sg_ids)->where('project_id', $projectId)->get();
                $now = date('Y-m-d H:i:s');
                $data = [];
                foreach ($sgs as $sg) {
                    $data[] = [
                        'dimming_task_id' => $taskId,
                        'sub_group_id' => $sg->id,
                        'created_at' => $now
                    ];
                }
                $task->update(['type' => 1]);
                DB::table('dimming_task_groups')->where('dimming_task_id', $taskId)->delete();
                DimmingTaskGroup::insert($data);
            }
            DB::commit();

            return back()->with('success', 'Task updated.');
        } catch (Throwable $t) {
            DB::rollBack();

            return back()->withErrors([
                'submit_error' => 'Something went wrong!'
            ]);
        }
    }

    public function rtuList(string $projectId)
    {
        return view('web.dimming-tasks.content-rtus', [
            'rtus' => RemoteTerminal::where('project_id', $projectId)->with(['concentrator'])->get()
        ]);
    }

    public function subGroupList(string $projectId)
    {
        return view('web.dimming-tasks.content', [
            'subGroups' => SubGroup::where('project_id', $projectId)->with(['group'])->get()
        ]);
    }

    public function addSchedule(string $projectId, string $taskId)
    {
        $task = DimmingTask::where('project_id', $projectId)
            ->where('id', $taskId)
            ->with('groups.subGroup', 'groups.rtu')
            ->firstOrFail();

        return view('web.dimming-tasks.add-schedule', [
            'task' => $task
        ]);
    }

    public function storeSchedule(Request $request, string $projectId, string $taskId)
    {
        $request->validate([
            'time' => 'required|date_format:H:i',
            'brightness' => ['required', 'integer', function ($attribute, $value, $fail) {
                if (!($value == 0 || ($value >= 12 && $value <= 100))) {
                    $fail('Brightness must be 0 or between 12 and 100.');
                }
            }],
        ]);

        $task = DimmingTask::where('project_id', $projectId)
            ->where('id', $taskId)
            ->with('groups.subGroup', 'groups.rtu')
            ->firstOrFail();

        DimmingTaskSchedule::create([
            'dimming_task_id' => $task->id,
            'time' => $request->time,
            'brightness' => $request->brightness,
        ]);

        return response()->json(['message' => 'Schedule added.']);
    }

    public function deleteSchedule(string $projectId, string $taskId, string $scheduleId)
    {
        $task = DimmingTask::where('project_id', $projectId)
            ->where('id', $taskId)
            ->firstOrFail();
        $schedule = DimmingTaskSchedule::where('id', $scheduleId)
            ->where('dimming_task_id', $taskId)
            ->firstOrFail();
        $schedule->delete();

        return to_route('dimming-task.add-rtus', ['project' => config('project_id'), 'task' => $task])
            ->with('success', 'Schedule deleted.');
    }
}
