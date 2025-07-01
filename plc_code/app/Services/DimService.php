<?php

namespace App\Services;

use App\Models\Luminary;
use App\Models\Remote\Command;
use App\Models\SubGroup;

class DimService
{
    public static function dimSubGroup(SubGroup $subGroup, $dimValue)
    {
        $luminaries = $subGroup->luminaries;
        $commands = [];
        if (!count($luminaries)) {
            return response()->json(['message' => 'Luminaries not found is this sub-group.'], 404);
        }

        foreach ($luminaries as $luminary) {
            if (!$luminary->concentrator || !$luminary->concentrator->concentrator_no) {
                continue;
            }

            $cmdHex = '5A0C00';
            $dcuId = formatDeviceIdForCommand($luminary->concentrator->concentrator_no);
            $cmdHex .= "{$dcuId}804006";
            $luminaryCmd = '5A0100';
            $luminaryId = formatDeviceIdForCommand($luminary->node_id);
            $luminaryCmd .= "{$luminaryId}80E0";
            $brightness = $dimValue < 0 ? 0 : ($dimValue > 100 ? 100 : $dimValue);
            $luminaryCmd .= str_pad(dechex($brightness), 2, "0", STR_PAD_LEFT);

            $luminaryCmd .= getCommandXor($luminaryCmd);
            $cmdHex .= $luminaryCmd;
            $cmdHex .= getCommandXor($cmdHex);

            $commands[] = [
                'command_hex' => strtoupper($cmdHex),
                'device_id' => $luminary->concentrator->concentrator_no,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }

        return $commands;
    }

    public static function dimLuminary(Luminary $luminary, $dimValue): bool
    {
        if (!$luminary->concentrator?->concentrator_no) {
            return false;
        }

        $cmdHex = '5A0C00';
        $dcuId = implode("", array_reverse(str_split($luminary->concentrator->concentrator_no, 2)));
        $cmdHex .= "{$dcuId}804006";
        $luminaryCmd = '5A0100';
        $luminaryId = implode("", array_reverse(str_split($luminary->node_id, 2)));
        $luminaryCmd .= "{$luminaryId}80E0";
        $brightness = $dimValue < 0 ? 0 : ($dimValue > 100 ? 100 : $dimValue);
        $luminaryCmd .= str_pad(dechex($brightness), 2, "0", STR_PAD_LEFT);

        $luminaryCmd .= getCommandXor($luminaryCmd);
        $cmdHex .= $luminaryCmd;
        $cmdHex .= getCommandXor($cmdHex);

        Command::create([
            'command_hex' => strtoupper($cmdHex),
            'device_id' => $dcuId,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return true;
    }
}