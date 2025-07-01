<?php

namespace Database\Seeders;

use App\Models\Concentrator;
use App\Models\ControlGearType;
use App\Models\Luminary;
use App\Models\LuminaryType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ControlGearTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cgt = [
            [
                'name' => 'Energy+',
            ],
        ];

        ControlGearType::insert($cgt);
    }
}
