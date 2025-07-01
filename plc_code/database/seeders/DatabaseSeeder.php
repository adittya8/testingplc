<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // $this->call(UserSeeder::class);
        // $this->call(ProjectSeeder::class);
        // $this->call(LightSourceTypeSeeder::class);
        // $this->call(BrandSeeder::class);
        // $this->call(PoleTypeSeeder::class);
        // $this->call(ZoneSeeder::class);
        // $this->call(DimmingTaskCateogrySeeder::class);
        // $this->call(GroupSeeder::class);
        // $this->call(ConcentratorSeeder::class);
        // $this->call(LuminaryTypeSeeder::class);
        // $this->call(ControlGearTypeSeeder::class);
        // $this->call(PoleSeeder::class);
        // $this->call(LampTypeSeeder::class);
        // $this->call(LuminarySeeder::class);
        // $this->call(BasePermissionSeeder::class);
        $this->call(PermissionSeeder::class);
    }
}
