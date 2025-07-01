<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    public function index()
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

        $projects = Project::get();
        $projects = $projects->map(function ($q) use ($concentratorsCount, $luminariesCount) {
            $cCount = $concentratorsCount->where('id', $q->id)->first();
            $q->concentrators_count = $cCount->concentrators_count ?? 0;

            $lCount = $luminariesCount->where('id', $q->id)->first();
            $q->luminaries_count = $lCount->luminaries_count ?? 0;

            return $q;
        });

        return view('web.projects.index', [
            'projects' => $projects,
        ]);
    }
}
