<?php

namespace Database\Seeders;

use App\Models\Concentrator;
use App\Models\Luminary;
use App\Models\LuminaryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LuminaryTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lums = [
            [
                'model' => 'Energy+',
                'light_source_type_id' => 1,
                'brand_id' => 1,
                'rated_power' => '100',
                'avg_life' => '50000',
            ],
        ];

        LuminaryType::insert($lums);
    }
}
