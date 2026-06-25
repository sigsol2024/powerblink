<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesSeeder::class);
        $this->call(AcademyPermissionsSeeder::class);
        $this->call(PowerblinkSiteSettingsSeeder::class);
        $this->call(CmsPagesSeeder::class);
        $this->call(PowerblinkDemoSeeder::class);
        $this->call(MediaSeeder::class);
    }
}
