<?php

namespace Database\Seeders;

use App\Models\ComplaintMeasures;
use Exception;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintMeasuresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            ComplaintMeasures::upsert([
                ['name' => 'Heating Controls'],
                ['name' => 'Heating Controls with TTZC'],
                ['name' => 'Heating Controls with TTZC and Smart Thermostat'],
                ['name' => 'Heating Controls with Smart thermostat'],
                ['name' => 'TRV'],
                ['name' => 'TTZC'],
                ['name' => 'Programmer & room thermostat'],
                ['name' => 'Smart thermostat'],
                ['name' => 'Weather/load Compensation'],
                ['name' => 'Loft Insulation'],
                ['name' => 'Boiler'],
                ['name' => 'Wall Insulation'],
                ['name' => 'FTCH'],
                ['name' => 'ASHP'],
                ['name' => 'UFI'],
                ['name' => 'Inspection'],
                ['name' => 'Remedial'],
                ['name' => 'Electric Storage Heaters'],
                ['name' => 'Solar PV'],
                ['name' => 'District Heating Connection or repair'],
                ['name' => 'Standard Alternative Methodology'],
                ['name' => 'Data Light Measure'],
                ['name' => 'Draught proofing'],
                ['name' => 'Single to double'],
                ['name' => 'Improved double'],
                ['name' => 'Window Glazing'],
                ['name' => 'Higher performance external doors'],
                ['name' => 'New Radiator'],
                ['name' => 'Roofline Extension'],
                ['name' => 'Pitched Roof Insulation'],
                ['name' => 'Flat Roof Insulation'],
                ['name' => 'Room in Roof insulation'],
                ['name' => 'Solid Floor Insulation'],
                ['name' => 'Park Home Insulation']
            ], uniqueBy: ['name'], update: ['updated_at']);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
