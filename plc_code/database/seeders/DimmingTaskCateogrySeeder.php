<?php

namespace Database\Seeders;

use App\Models\DimmingTaskCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DimmingTaskCateogrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Lora',
            ],
            [
                'name' => 'Loop',
            ],
            [
                'name' => 'PLC',
            ],
            [
                'name' => 'WF',
            ],
        ];

        DimmingTaskCategory::insert($categories);
    }
}
