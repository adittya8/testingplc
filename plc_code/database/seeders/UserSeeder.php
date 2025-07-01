<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'mobile' => '01700000000',
            'username' => 'admin',
            'password' => bcrypt('admin'),
        ]);

        $projectAdmin = User::create([
            'name' => 'Project Admin',
            'email' => 'p.admin@example.com',
            'mobile' => '01700000001',
            'username' => 'project_admin',
            'password' => bcrypt('admin'),
        ]);

        Role::create(['name' => 'Super Admin']);
        Role::create(['name' => 'Project Admin']);
        Role::create(['name' => 'User']);

        $superAdmin->assignRole('Super Admin');
        $projectAdmin->assignRole('Project Admin');
    }
}
