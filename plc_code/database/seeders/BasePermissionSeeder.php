<?php

namespace Database\Seeders;

use App\Models\BasePermission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BasePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'Luminaries Config',
            'Zone Management',
            'Grouping',
            'DCU',
            'Pole',
            'Luminaries',
            'Dimming Task',
            'Dimming Schedule',
            'Monitor Log',
            'Lamp Data',
            'Alarms',
            'Luminaries Points',
            'Power Consumption',
        ];

        foreach ($permissions as $perm) {
            BasePermission::create(['name' => $perm]);
        }
    }
}
