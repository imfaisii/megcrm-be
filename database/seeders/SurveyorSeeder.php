<?php

namespace Database\Seeders;

use App\Models\Surveyor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SurveyorSeeder extends Seeder
{
    protected int $total = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < $this->total; $i++) {
            Surveyor::firstOrCreate([
                'name' => "Surveyor $i"
            ]);
        }
    }
}
