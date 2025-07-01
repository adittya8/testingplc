<?php

namespace Database\Seeders;

use App\Models\Group;
use App\Models\SubGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groups = [
            [
                'name' => 'Farmgate to Staff Road',
                'sub_groups' => [
                    [
                        'name' => 'Farmgate',
                    ],
                    [
                        'name' => 'Bijoy Shoroni',
                    ],
                ],
            ]
        ];

        foreach ($groups as $group) {
            $newGroup = Group::create([
                'name' => $group['name'],
                'project_id' => 1,
            ]);

            if (isset($group['sub_groups']) && count($group['sub_groups'])) {
                SubGroup::insert(array_map(function ($q) use ($newGroup) {
                    $q['group_id'] = $newGroup->id;
                    return $q;
                }, $group['sub_groups']));
            }
        }
    }
}
