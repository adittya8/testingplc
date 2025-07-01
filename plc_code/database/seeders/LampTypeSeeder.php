<?php

namespace Database\Seeders;

use App\Models\LampType;
use App\Models\Pole;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LampTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $lts = [
            [
                'name' => 'Single Color Temparature',
            ],
            [
                'name' => 'Double Color Temparature',
            ]
        ];

        LampType::insert($lts);
    }
}
