<?php

namespace App\Services;

class CommandService
{
    public static function getRtuDimmingCommand(string $rtuCode, string $dcuCode, int $brightness): array
    {
        $cmdHex = "8A0006{$rtuCode}FF15001E";
        $cmdHex .= str_pad(dechex($brightness), 2, "0", STR_PAD_LEFT);
        $cmdHex .= '320032';
        $cmdHex .= getCommandXor($cmdHex);

        $id = floor(microtime(true) * 1000);
        $ti = substr($id, 0, -1);

        return [
            'command' => [
                "pa" => [
                    "ti" => (int) $ti,
                    "va" => [
                        "ddid" => $rtuCode,
                        "u8data" => strtoupper($cmdHex),
                    ]
                ],
                "ac" => 1,
                "id" => $id,
                "idi" => "method.ddtc",
                "ve" => "1.0"
            ],
            'channel' => "/mqtt-plc-center/{$dcuCode}/event/down"
        ];
    }
}
