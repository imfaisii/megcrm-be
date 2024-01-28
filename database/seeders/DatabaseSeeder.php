<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            JobTypeSeeder::class,
            FuelTypeSeeder::class,
            BenefitTypeSeeder::class,
            LeadGeneratorSeeder::class,
            LeadSourceSeeder::class,
            MeasureSeeder::class,
            SurveyorSeeder::class,
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class
        ]);
    }
}
