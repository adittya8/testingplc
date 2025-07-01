<?php

namespace App\Http\Controllers\Web;

use App\Enums\LuminaryStatuses;
use App\Http\Controllers\Controller;
use App\Models\Alert;
use App\Models\PowerConsumption;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(string $projectId)
    {
        $reportDataSub = DB::table('report_data')->whereBetween('created_at', [date('Y-m-d 00:00:00'), date('Y-m-d 23:59:59')]);
        $luminaries = DB::table('remote_terminals as RTU')
            ->where('RTU.project_id', $projectId)
            ->select(
                DB::raw("SUM(CASE WHEN status_updated_at >= DATE_SUB(NOW(), INTERVAL 6 MINUTE) THEN 1 ELSE 0 END) as online_count"),
                DB::raw("SUM(CASE WHEN status_updated_at < DATE_SUB(NOW(), INTERVAL 6 MINUTE) THEN 1 ELSE 0 END) as offline_count"),
                DB::raw('0 as alarm_count'),
                DB::raw("SUM(CASE WHEN status_updated_at IS NULL THEN 1 ELSE 0 END) as other_count"),
            )
            ->first();

        $concentrators = DB::table('concentrators')
            ->where('project_id', $projectId)
            ->select(
                DB::raw("COUNT(CASE WHEN status_updated_at >= DATE_SUB(NOW(), INTERVAL 3 MINUTE) THEN 1 END) AS online_count"),
                DB::raw("COUNT(CASE WHEN status_updated_at < DATE_SUB(NOW(), INTERVAL 3 MINUTE) THEN 1 END) AS offline_count"),
                DB::raw('SUM(CASE WHEN last_status = ' . LuminaryStatuses::ALARM->value . ' THEN 1 ELSE 0 END) as alarm_count'),
                DB::raw('0 as other_count'),
            )
            ->first();

        $dateFrom = date('Y-m-d', strtotime('-14 days')) . ' 00:00:00';
        $dateGroups = collect([
            [
                'start' => $dateFrom,
                'end' => date('Y-m-d', strtotime('+4 days', strtotime($dateFrom))) . ' 23:59:59',
                'label' => date('d M', strtotime($dateFrom)) . " - " . date('d M', strtotime('+4 days', strtotime($dateFrom)))
            ],
            [
                'start' => date('Y-m-d', strtotime('+5 days', strtotime($dateFrom))) . ' 00:00:00',
                'end' => date('Y-m-d', strtotime('+9 days', strtotime($dateFrom))) . ' 23:59:59',
                'label' => date('d M', strtotime('+5 days', strtotime($dateFrom))) . " - " . date('d M', strtotime('+9 days', strtotime($dateFrom)))
            ],
            [
                'start' => date('Y-m-d', strtotime('+10 days', strtotime($dateFrom))) . ' 00:00:00',
                'end' => date('Y-m-d', strtotime('+14 days', strtotime($dateFrom))) . ' 23:59:59',
                'label' => date('d M', strtotime('+10 days', strtotime($dateFrom))) . " - " . date('d M', strtotime('+14 days', strtotime($dateFrom)))
            ]
        ]);

        $alarmCounts = $dateGroups->map(function ($dg) {
            $count = Alert::whereBetween('created_at', [$dg['start'], $dg['end']])
                ->select(
                    DB::raw("COUNT(CASE WHEN rtu_code IS NULL THEN 1 END) as concentrator"),
                    DB::raw("COUNT(CASE WHEN dcu_code IS NULL THEN 1 END) as luminary")
                )->first();

            return $dg += [
                'concentrator_count' => $count->concentrator,
                'luminary_count' => $count->luminary,
            ];
        });

        $powerConsumptions = $dateGroups->map(function ($dg) {
            $count = PowerConsumption::whereBetween('device_time', [$dg['start'], $dg['end']])->sum('power');

            return $dg += [
                'total_consumption' => $count,
            ];
        });

        return view('web.dashboard', [
            'luminaryStatus' => $luminaries,
            'concentratorStatus' => $concentrators,
            'alarmCounts' => $alarmCounts,
            'powerConsumptions' => $powerConsumptions,
        ]);
    }

    public function setLocale(string $locale)
    {
        App::setLocale($locale);

        Auth::user()->update([
            'locale' => $locale
        ]);

        return back()->with('success', 'Language changed.');
    }
}
