<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(string $projectId)
    {
        $logs = DB::table('activity_log as AL')
            ->join('users as U', 'U.id', '=', 'AL.causer_id')
            ->where('causer_type', 'App\Models\User')
            ->select(
                'AL.id',
                'AL.event',
                DB::raw("SUBSTRING_INDEX(subject_type, '\\\\', -1) as subject"),
                'subject_id',
                'U.id as user_id',
                'U.name as user',
                'AL.created_at',
            )
            ->orderByDesc('id')
            ->paginate(50);

        return view('web.logs.index', compact('logs'));
    }
}
