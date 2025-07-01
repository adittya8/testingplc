<?php

namespace App\Http\Controllers\Web;

use App\Enums\CommandTypes;
use App\Http\Controllers\Controller;
use App\Models\Command;
use App\Models\Concentrator;
use App\Models\Group;
use App\Models\SubGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpMqtt\Client\MqttClient;

class GroupDimmingController extends Controller
{
    public function dimGroup(Request $request, string $id)
    {
        if (!validateBrightness($request->dim, $request->power)) {
            return response()->json(['message' => 'Please provide a valid brightness value. (0 or  between 12 and 100)'], 400);
        }

        $group = Group::with(['rtus.concentrator'])->findOrFail($id);
        $rtus = $group->rtus;
        $commands = [];
        if (!$rtus || !count($rtus)) {
            return response()->json(['message' => 'No RTU available in this group.'], 404);
        }

        $dimValue = $request->dim ?? 0;
        $power = $request->power;
        $rtuGroups = $rtus->groupBy('concentrator_id');
        $dcus = Concentrator::whereIn('id', $rtus->pluck('concentrator_id'))->get();

        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), env('MQTT_CLIENT_ID'));
        if (!$mqtt->isConnected()) {
            $mqtt->connect();
        }

        foreach ($rtuGroups as $dcuId => $rtuGroup) {
            $dcu = $dcus->where('id', $dcuId)->first();
            if (!$dcu || !$dcu->concentrator_no) {
                continue;
            }

            $mqttEventChannel = "/mqtt-plc-center/{$dcu->concentrator_no}/event/down";

            foreach ($rtuGroup as $rtu) {
                if ($power > 0 && $rtu->rated_power) {
                    $dimValue = $power > $rtu->rated_power ? 100 : number_format(ceil($power / $rtu->rated_power * 100));
                    $dimValue = $dimValue < 12 && $dimValue > 0 ? 12 : $dimValue;
                    $dimValue = $dimValue > 100 ? 100 : $dimValue;
                }

                $cmdHex = "8A0006{$rtu->code}FF15001E";
                $dimHex = str_pad(dechex($dimValue), 2, "0", STR_PAD_LEFT);
                $cmdHex .= $dimHex;
                $cmdHex .= '320032';
                $cmdHex .= getCommandXor($cmdHex);

                $id = floor(microtime(true) * 1000);
                $ti = substr($id, 0, -1);
                $commandRtu = [
                    "pa" => [
                        "ti" => (int) $ti,
                        "va" => [
                            "ddid" => $rtu->code,
                            "u8data" => strtoupper($cmdHex),
                        ]
                    ],
                    "ac" => 1,
                    "id" => $id,
                    "idi" => "method.ddtc",
                    "ve" => "1.0"
                ];
                $commands[] = [
                    'command' => $commandRtu,
                    'channel' => $mqttEventChannel,
                    'command_type' => CommandTypes::GROUP_DIMMING,
                    'commandable_type' => 'App\Models\RemoteTerminal',
                    'commandable_id' => $rtu->id,
                    'rtu' => $rtu,
                ];
                usleep(20000);
            }
        }

        $dbCommands = [];
        foreach ($commands as $cmd) {
            $command = json_encode($cmd['command']);
            $mqtt->publish($cmd['channel'], $command, 0);
            $dbCommands[] = [
                'command' => $command,
                'commandable_type' => $cmd['commandable_type'],
                'commandable_id' => $cmd['commandable_id'],
                'command_type' => $cmd['command_type'],
                'is_sent' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            sleep(1);
        }
        $mqtt->disconnect();
        Command::insert($dbCommands);

        activity('dimming')
            ->causedBy(Auth::user())
            ->event('group dimming')
            ->performedOn($group)
            ->withProperties(['commands' => $dbCommands])
            ->log('group dimming');

        return response()->json(['message' => 'Sent dimming command for group.']);
    }

    public function dimSubGroup(Request $request, string $id)
    {
        if (!validateBrightness($request->dim, $request->power)) {
            return response()->json(['message' => 'Please provide a valid brightness value. (0 or  between 12 and 100)'], 400);
        }

        $subGroup = SubGroup::with(['rtus.concentrator'])->findOrFail($id);
        $rtus = $subGroup->rtus;
        $commands = [];
        if (!$rtus || !count($rtus)) {
            return response()->json(['message' => 'No RTU available in this sub-group.'], 404);
        }

        $dimValue = $request->dim ?? 0;
        $power = $request->power;
        $rtuGroups = $rtus->groupBy('concentrator_id');
        $dcus = Concentrator::whereIn('id', $rtus->pluck('concentrator_id'))->get();

        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), env('MQTT_CLIENT_ID'));
        if (!$mqtt->isConnected()) {
            $mqtt->connect();
        }

        $devCom = [];
        foreach ($rtuGroups as $dcuId => $rtuGroup) {
            $dcu = $dcus->where('id', $dcuId)->first();
            if (!$dcu || !$dcu->concentrator_no) {
                continue;
            }

            $mqttEventChannel = "/mqtt-plc-center/{$dcu->concentrator_no}/event/down";

            foreach ($rtuGroup as $rtu) {
                if ($power > 0 && $rtu->rated_power) {
                    $dimValue = $power > $rtu->rated_power ? 100 : number_format(ceil($power / $rtu->rated_power * 100));
                    $dimValue = $dimValue < 12 && $dimValue > 0 ? 12 : $dimValue;
                    $dimValue = $dimValue > 100 ? 100 : $dimValue;
                }

                $cmdHex = "8A0006{$rtu->code}FF15001E";
                $dimHex = str_pad(dechex($dimValue), 2, "0", STR_PAD_LEFT);
                $cmdHex .= $dimHex;
                $cmdHex .= '320032';
                $cmdHex .= getCommandXor($cmdHex);

                $id = floor(microtime(true) * 1000);
                $ti = substr($id, 0, -1);
                $commandRtu = [
                    "pa" => [
                        "ti" => (int) $ti,
                        "va" => [
                            "ddid" => $rtu->code,
                            "u8data" => strtoupper($cmdHex),
                        ]
                    ],
                    "ac" => 1,
                    "id" => $id,
                    "idi" => "method.ddtc",
                    "ve" => "1.0"
                ];
                $devCom[] = $commandRtu;
                $commands[] = [
                    'command' => $commandRtu,
                    'channel' => $mqttEventChannel,
                    'command_type' => CommandTypes::GROUP_DIMMING,
                    'commandable_type' => 'App\Models\RemoteTerminal',
                    'commandable_id' => $rtu->id,
                    'rtu' => $rtu,
                ];

                usleep(20000);
            }
        }

        $dbCommands = [];
        foreach ($commands as $cmd) {
            $command = json_encode($cmd['command']);
            $mqtt->publish($cmd['channel'], $command, 0);
            $dbCommands[] = [
                'command' => $command,
                'commandable_type' => $cmd['commandable_type'],
                'commandable_id' => $cmd['commandable_id'],
                'command_type' => $cmd['command_type'],
                'is_sent' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ];
            sleep(1);
        }
        $mqtt->disconnect();
        Command::insert($dbCommands);

        activity('dimming')
            ->causedBy(Auth::user())
            ->event('sub-group dimming')
            ->performedOn($subGroup)
            ->withProperties(['commands' => $dbCommands])
            ->log('sub-group dimming');

        return response()->json([
            'message' => 'Sent dimming command for sub-group.',
            'commands' => $devCom
        ]);
    }
}
