<?php

namespace Database\Seeders;

use App\Models\Road;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $zones = [
            [
                'name' => 'Farmgate to Staff Road',
                'roads' => [
                    [
                        'name' => 'Farmgate',
                        'grade' => 'Trunk Road',
                        'length' => '11',
                    ],
                    [
                        'name' => 'Bijoy Shoroni',
                        'grade' => 'Trunk Road',
                        'length' => '11',
                    ],
                    [
                        'name' => 'Mohakhali',
                        'grade' => 'Trunk Road',
                        'length' => '2',
                    ],
                    [
                        'name' => 'Banani',
                        'grade' => 'Trunk Road',
                        'length' => '5',
                    ],
                    [
                        'name' => 'Staff Road',
                        'grade' => 'Trunk Road',
                        'length' => '9',
                    ],
                ]
            ],
        ];

        foreach ($zones as $zone) {
            $newZone = Zone::create([
                'name' => $zone['name'],
                'project_id' => 1,
            ]);

            if (isset($zone['roads']) && count($zone['roads'])) {
                Road::insert(array_map(function ($q) use ($newZone) {
                    $q['zone_id'] = $newZone->id;
                    $q['project_id'] = 1;
                    return $q;
                }, $zone['roads']));
            }
        }
    }
}
