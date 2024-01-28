<?php

namespace Database\Seeders;

use App\Models\FuelType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FuelTypeSeeder extends Seeder
{
    protected array $entries = [
        'Main Gas',
        'Solid Fuel',
        'LPG',
        'Oil',
        'Electric',
        'Not Yet Known',
        'ASHP',
        'Biomass',
        'DHS',
        'GSHP'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $fuelType) {
            FuelType::firstOrCreate([
                'name' => $fuelType
            ]);
        }
    }
}
