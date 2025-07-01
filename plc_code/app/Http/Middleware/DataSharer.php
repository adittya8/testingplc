<?php

namespace App\Http\Middleware;

use App\Models\Concentrator;
use App\Models\Group;
use App\Models\Project;
use App\Models\RemoteTerminal;
use App\Models\SubGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class DataSharer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $projectId = $request->route()->parameter('project');
        if ($projectId) {
            Config::set('project_id', $projectId);
            Config::set('project', Project::find($projectId));

            $rtus = RemoteTerminal::where('project_id', $projectId)->get();
            Config::set('rtus', $rtus);

            $dcus = Concentrator::where('project_id', $projectId)->get();
            Config::set('dcus', $dcus);

            $groups = Group::where('project_id', $projectId)->withCount('rtus')->get();
            Config::set('groups', $groups);

            $subGroups = SubGroup::where('project_id', $projectId)->withCount('rtus')->get();
            Config::set('subGroups', $subGroups);
        }

        return $next($request);
    }
}
