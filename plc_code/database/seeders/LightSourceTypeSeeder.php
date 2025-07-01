<?php

namespace Database\Seeders;

use App\Models\LightSourceType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LightSourceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'LED',
            ],
            [
                'name' => 'HID',
            ],
            [
                'name' => 'Cosmo',
            ],
            [
                'name' => 'HPS',
            ],
            [
                'name' => 'LPS',
            ],
            [
                'name' => 'Metal Halide',
            ],
            [
                'name' => 'Other',
            ],
        ];

        LightSourceType::insert($types);
    }
}
