<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DimmingTaskController;
use App\Http\Controllers\Web\GroupingController;
use App\Http\Controllers\Web\IndividualDimmingController;
use App\Http\Controllers\Web\GroupDimmingController;
use App\Http\Controllers\Web\LogController;
use App\Http\Controllers\Web\LuminariesConfigController;
use App\Http\Controllers\Web\ZoneManagementController;
use App\Livewire\Alarms;
use App\Livewire\Concentrator;
use App\Livewire\DimmingSchedule;
use App\Livewire\DimmingScheduleCreate;
use App\Livewire\DimmingTask;
use App\Livewire\DimmingTaskCreate;
use App\Livewire\EquipmentAlarms;
use App\Livewire\LampData;
use App\Livewire\LuminariesLifespan;
use App\Livewire\LuminariesPoint;
use App\Livewire\Luminary;
use App\Livewire\MonitorLog;
use App\Livewire\Pole;
use App\Livewire\PowerConsumption;
use App\Livewire\Project;
use App\Livewire\RemoteTerminal;
use App\Livewire\Role;
use App\Livewire\RolePermission;
use App\Livewire\SchedulePreset;
use App\Livewire\SMSAlerts;
use App\Livewire\User;
use App\Models\RemoteTerminal as RemoteTerminalModel;
use App\Services\RTUService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'loginForm'])->name('login.form');
    Route::post('login', [AuthController::class, 'login'])->name('login');
});

Route::middleware(['auth', 'locale'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::post('run-command', [DashboardController::class, 'run'])->name('run-command');
    Route::get('set-locale/{locale}', [DashboardController::class, 'setLocale'])->name('set-locale');

    Route::middleware('share')->group(function () {
        Route::get('/', Project::class)->name('home');
        Route::get('dashboard', fn() => to_route('home'))->name('dashboard');
        Route::get('projects', fn() => to_route('home'))->name('projects');
        Route::post('individual-dimming/luminary/{id}', [IndividualDimmingController::class, 'dimLuminary'])->name('dimming-individual.luminary');
    });

    // Route::post('individual-dimming/{id}', [IndividualDimmingController::class, 'dimLuminary'])->name('individual-dimming');
    Route::post('individual-dimming/{id}', [IndividualDimmingController::class, 'dimRtu'])->name('individual-dimming');
    Route::post('group-dimming/{id}', [GroupDimmingController::class, 'dimGroup'])->name('group-dimming');
    // Route::post('group-dimming/{id}', [IndividualDimmingController::class, 'dimDcu'])->name('group-dimming');
    Route::post('sub-group-dimming/{id}', [GroupDimmingController::class, 'dimSubGroup'])->name('sub-group-dimming');
    Route::get('rtu-dimming/{id}', [IndividualDimmingController::class, 'dimRtu'])->name('rtu-dimming');
    Route::post('dcu-dimming/{id}', [IndividualDimmingController::class, 'dimDcu'])->name('dcu-dimming');

    Route::prefix('{project}')->middleware('share')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('projects.dashboard');

        Route::get('luminaries-config', [LuminariesConfigController::class, 'index'])->name('luminaries-config');
        Route::get('zone-management', [ZoneManagementController::class, 'index'])->name('zone-management');
        Route::get('grouping', [GroupingController::class, 'index'])->name('grouping');
        Route::get('dcus', Concentrator::class)->name('concentrators');
        Route::get('poles', Pole::class)->name('poles');
        Route::get('rtus', RemoteTerminal::class)->name('rtus');
        Route::get('luminaries', Luminary::class)->name('luminaries');

        Route::get('schedule-presets', SchedulePreset::class)->name('schedule-presets');

        Route::get('dimming-task', DimmingTask::class)->name('dimming-task');
        Route::get('dimming-task/create', DimmingTaskCreate::class)->name('dimming-task.create');
        Route::get('dimming-task/{task}/edit', DimmingTaskCreate::class)->name('dimming-task.edit');
        Route::get('dimming-task/{task}/add-rtus', [DimmingTaskController::class, 'addRtus'])->name('dimming-task.add-rtus');
        Route::get('dimming-task/{task}/store-rtus', [DimmingTaskController::class, 'storeRtus'])->name('dimming-task.store-rtus');
        Route::get('dimming-task/rtu-list', [DimmingTaskController::class, 'rtuList'])->name('dimming-task.rtu-list');
        Route::get('dimming-task/sg-list', [DimmingTaskController::class, 'subGroupList'])->name('dimming-task.sub-group-list');
        Route::get('dimming-task/{task}/add-schedule', [DimmingTaskController::class, 'addSchedule'])->name('dimming-task.add-schedule');
        Route::post('dimming-task/{task}/store-schedule', [DimmingTaskController::class, 'storeSchedule'])->name('dimming-task.store-schedule');
        Route::delete('dimming-task/{task}/delete-schedule/{schedule}', [DimmingTaskController::class, 'deleteSchedule'])->name('dimming-task.delete-schedule');

        Route::get('dimming-schedule', DimmingSchedule::class)->name('dimming-schedule');
        Route::get('dimming-schedule/create', DimmingScheduleCreate::class)->name('dimming-schedule.create');
        Route::get('dimming-schedule/{schedule}/edit', DimmingScheduleCreate::class)->name('dimming-schedule.edit');

        Route::get('lamp-data', LampData::class)->name('lamp-data');
        Route::get('monitor-log', MonitorLog::class)->name('monitor-log');

        Route::get('alerts', Alarms::class)->name('alerts');
        Route::get('luminaries-point', LuminariesPoint::class)->name('luminaries-point');
        Route::get('power-consumption', PowerConsumption::class)->name('power-consumption');

        Route::get('equipment-alarms', EquipmentAlarms::class)->name('equipment-alarms');
        Route::get('sms-alerts', SMSAlerts::class)->name('sms-alerts');
        Route::get('luminaries-lifespan', LuminariesLifespan::class)->name('luminaries-lifespan');

        Route::post('send-schedule/{id}', [IndividualDimmingController::class, 'schedule'])->name('schedule-command');

        Route::get('users', User::class)->name('users');

        Route::get('roles/{role}/permissions', RolePermission::class)->name('roles.permissions');
        Route::get('roles', Role::class)->name('roles');

        Route::get('logs', [LogController::class, 'index'])->name('logs.index');
    });
});

Route::get('tt', function (Request $request) {
    $device_id = implode("", array_reverse(str_split($request->device_id, 2)));
    $cmd = '29';
    $relays = $request->command == 1 ? '01010100' : '01010000';

    $ts = strtotime($request->date_to);

    $y = dechex2digits(date('y', $ts));
    $m = dechex2digits(date('m', $ts));
    $d = dechex2digits(date('d', $ts));
    $h = dechex2digits(date('H', $ts));
    $i = dechex2digits(date('i', $ts));
    $s = dechex2digits(date('s', $ts));

    $hex = "5A0C00{$device_id}80{$cmd}{$relays}{$y}{$m}{$d}{$h}{$i}{$s}";
    $hex .= $request->command == 1 ? '0B00' : '0000';
    $hexWithChecksum = strtoupper($hex . getCommandXor($hex));

    return $hexWithChecksum;
});

Route::get('test', function () {
    // $mqtt = new \PhpMqtt\Client\MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'bond_plc');
    // $mqtt->connect();
    // $mqtt->publish('php-mqtt/client/test', 'Hello World!', 0);
    // $mqtt->disconnect();

    // $dcuCode = explode('/', $channel)[2];
    // $rtu = RemoteTerminalModel::where('code', '5F100106D1')->first();

    return RTUService::syncRtuToDcu(1);
});

// Route::get('individual-dimming/luminary/{id}', [IndividualDimmingController::class, 'dimLuminary'])->name('dimming-individual.luminary');
