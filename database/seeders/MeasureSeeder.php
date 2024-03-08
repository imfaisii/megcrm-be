<?php

namespace Database\Seeders;

use App\Models\Measure;
use Illuminate\Database\Seeder;

class MeasureSeeder extends Seeder
{
    protected array $entries = [
        'Loft Insulation',
        'Boiler',
        'Wall Insulation',
        'FTCH',
        'ASHP',
        'Heating Controls',
        'TRV',
        'TTZC',
        'UFI',
        'New Radiator',
        'Roofline Extension',
        'Pitched Roof Insulation',
        'Flat Roof Insulation',
        'Room in Roof insulation',
        'Solid Floor Insulation',
        'Electric Storage Heaters',
        'Programmer & room thermostat',
        'Smart thermostat',
        'Weather/load Compensation',
        'Park Home Insulation',
        'Solar PV',
        'District Heating Connection or repair',
        'Standard Alternative Methodology',
        'Data Light Measure',
        'Draught proofing',
        'Single to double',
        'Improved double',
        'Window Glazing',
        'Higher performance external doors',
        'Inspection',
        'Remedial',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $measure) {
            Measure::firstOrCreate([
                'name' => $measure,
            ]);
        }
    }
}
