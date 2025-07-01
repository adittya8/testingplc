<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'View Dashboard', 'group' => 1],

            ['name' => 'View Brands', 'group' => 2],
            ['name' => 'Create Brands', 'group' => 2],
            ['name' => 'Edit Brands', 'group' => 2],
            ['name' => 'Delete Brands', 'group' => 2],

            ['name' => 'View Luminary-Types', 'group' => 3],
            ['name' => 'Create Luminary-Types', 'group' => 3],
            ['name' => 'Edit Luminary-Types', 'group' => 3],
            ['name' => 'Delete Luminary-Types', 'group' => 3],

            ['name' => 'View Zones', 'group' => 4],
            ['name' => 'Create Zones', 'group' => 4],
            ['name' => 'Edit Zones', 'group' => 4],
            ['name' => 'Delete Zones', 'group' => 4],

            ['name' => 'View Roads', 'group' => 5],
            ['name' => 'Create Roads', 'group' => 5],
            ['name' => 'Edit Roads', 'group' => 5],
            ['name' => 'Delete Roads', 'group' => 5],

            ['name' => 'View Groups', 'group' => 6],
            ['name' => 'Create Groups', 'group' => 6],
            ['name' => 'Edit Groups', 'group' => 6],
            ['name' => 'Delete Groups', 'group' => 6],
            ['name' => 'Dim Groups', 'group' => 6],

            ['name' => 'View Sub-Groups', 'group' => 7],
            ['name' => 'Create Sub-Groups', 'group' => 7],
            ['name' => 'Edit Sub-Groups', 'group' => 7],
            ['name' => 'Delete Sub-Groups', 'group' => 7],
            ['name' => 'Dim Sub-Groups', 'group' => 7],

            ['name' => 'View DCUs', 'group' => 8],
            ['name' => 'Create DCUs', 'group' => 8],
            ['name' => 'Edit DCUs', 'group' => 8],
            ['name' => 'Delete DCUs', 'group' => 8],
            ['name' => 'Dim DCUs', 'group' => 8],
            ['name' => 'Schedule DCUs', 'group' => 8],

            ['name' => 'View RTUs', 'group' => 9],
            ['name' => 'Create RTUs', 'group' => 9],
            ['name' => 'Edit RTUs', 'group' => 9],
            ['name' => 'Delete RTUs', 'group' => 9],
            ['name' => 'Dim RTUs', 'group' => 9],

            ['name' => 'View Poles', 'group' => 10],
            ['name' => 'Create Poles', 'group' => 10],
            ['name' => 'Edit Poles', 'group' => 10],
            ['name' => 'Delete Poles', 'group' => 10],

            ['name' => 'View Luminaries', 'group' => 11],
            ['name' => 'Create Luminaries', 'group' => 11],
            ['name' => 'Edit Luminaries', 'group' => 11],
            ['name' => 'Delete Luminaries', 'group' => 11],

            ['name' => 'View Schedule Presets', 'group' => 12],
            ['name' => 'Create Schedule Presets', 'group' => 12],
            ['name' => 'Edit Schedule Presets', 'group' => 12],
            ['name' => 'Delete Schedule Presets', 'group' => 12],

            ['name' => 'View Schedule Tasks', 'group' => 13],
            ['name' => 'Create Schedule Tasks', 'group' => 13],
            ['name' => 'Edit Schedule Tasks', 'group' => 13],
            ['name' => 'Delete Schedule Tasks', 'group' => 13],

            ['name' => 'View Monitor Log', 'group' => 14],
            ['name' => 'View Lamp Data', 'group' => 14],

            ['name' => 'View Alerts', 'group' => 15],
            ['name' => 'View Luminary Points', 'group' => 15],
            ['name' => 'View Power Consumption', 'group' => 15],
            ['name' => 'View Equipment Alarms', 'group' => 15],
            ['name' => 'View SMS Alerts', 'group' => 15],
            ['name' => 'View Luminaries Lifespan', 'group' => 15],

            ['name' => 'View Users', 'group' => 16],
            ['name' => 'Create Users', 'group' => 16],
            ['name' => 'Edit Users', 'group' => 16],
            ['name' => 'Delete Users', 'group' => 16],

            ['name' => 'View Roles', 'group' => 17],
            ['name' => 'Create Roles', 'group' => 17],
            ['name' => 'Edit Roles', 'group' => 17],
            ['name' => 'Delete Roles', 'group' => 17],

            ['name' => 'View Logs', 'group' => 18],
        ];

        $data = [];
        foreach ($permissions as $perm) {
            $data[] = [
                'name' => $perm['name'],
                'group_id' => $perm['group'],
                'guard_name' => 'web',
                'type' => 'project',
            ];
        }
        DB::table('permissions')->insert($data);

        foreach ($permissions as $perm) {
            $role = Role::where('name', 'Project Admin')->first();
            $role->givePermissionTo($perm['name']);
        }
    }
}
