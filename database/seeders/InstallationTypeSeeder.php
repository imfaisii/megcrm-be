<?php

namespace Database\Seeders;

use App\Models\InstallationType;
use Illuminate\Database\Seeder;

class InstallationTypeSeeder extends Seeder
{
    protected $entries = [
        'Boiler Engineer',
        'Loft Installer',
        'Under Floor Insulation',
        'Enternal Wall Insulation',
        'Internal Wall Insulation',
    ];

    public function run(): void
    {
        foreach ($this->entries as $key => $entry) {
            InstallationType::firstOrCreate([
                'name' => $entry,
                'created_by_id' => 1,
            ]);
        }
    }
}
