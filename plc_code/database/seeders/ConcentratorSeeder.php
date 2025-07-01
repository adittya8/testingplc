<?php

namespace Database\Seeders;

use App\Models\Concentrator;
use App\Models\Luminary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConcentratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cons = [
            [
                'name' => 'Energy+',
                'road_id' => 1,
                'concentrator_no' => '27080109',
                'sim_no' => '0283384849',
            ],
        ];

        Concentrator::insert($cons);
    }
}
