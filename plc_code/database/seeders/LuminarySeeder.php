<?php

namespace Database\Seeders;

use App\Models\Luminary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LuminarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Luminary::create([
            'node_id' => '6A100001',
            'lamp_type_id' => 1,
            'concentrator_id' => 1,
            'sub_group_id' => 1,
            'luminary_type_id' => 1,
            'control_gear_type_id' => 1,
            'pole_id' => 1,
            'installation_status' => 1,
            'created_at' => now(),
        ]);
        Luminary::factory(9)->create();
    }
}
