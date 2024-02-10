<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\CallCenterStatus;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $local = app()->environment('local');

        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            AdminSeeder::class,

            JobTypeSeeder::class,
            FuelTypeSeeder::class,
            BenefitTypeSeeder::class,
            LeadGeneratorSeeder::class,
            LeadSourceSeeder::class,
            MeasureSeeder::class,
            SurveyorSeeder::class,
            CallCenterStatus::class,

            //! Always after adminseeder
            LeadStatusSeeder::class
        ]);

        if ($local) {
            // factories
            User::factory()->count(20)->create();
        }

        Lead::factory()->count($local ? 20 : 5)->create();
    }
}
