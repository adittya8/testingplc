<?php

namespace App\Jobs;

use App\Models\Concentrator;
use App\Models\DeviceData;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class StoreDeviceData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected string $topic, protected string $message)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //sample topic => /mqtt-plc-center/241001002F/service/up
        $explodedTopic = explode('/', $this->topic);
        $key = array_search('mqtt-plc-center', $explodedTopic);
        if (!array_key_exists($key + 1, $explodedTopic)) {
            return;
        }

        $deviceId = $explodedTopic[$key + 1];
        $device = Concentrator::where('concentrator_no', $deviceId)->first();
        if (!$device) {
            return;
        }

        DeviceData::create([
            'topic' => $this->topic,
            'data' => $this->message,
            'device_id' => $deviceId,
        ]);
    }
}
