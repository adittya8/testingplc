<?php

namespace Database\Seeders;

use App\Models\PoleType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PoleTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Single Arm',
            ],
            [
                'name' => 'Double Arm',
            ],
        ];

        PoleType::insert($types);
    }
}
