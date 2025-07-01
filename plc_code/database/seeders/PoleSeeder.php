<?php

namespace Database\Seeders;

use App\Models\Pole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $poles = [
            [
                'code' => 12034,
                'road_id' => 1,
                'concentrator_id' => 1,
                'pole_type_id' => 1,
                'serial' => 1,
            ],
        ];

        Pole::insert($poles);
    }
}
