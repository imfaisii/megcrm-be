<?php

namespace Database\Seeders;

use App\Models\LeadSource;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadSourceSeeder extends Seeder
{
    protected array $entries = [
        "Facebook",
        "Google",
        "Leaflet",
        "Door Knocking",
        "Door Referral",
        "Events",
        "Email Marketing",
        "Content marketing",
        "Direct mail",
        "Cold calling",
        "Partnerships"
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $leadSource) {
            LeadSource::firstOrCreate([
                'name' => $leadSource
            ]);
        }
    }
}
