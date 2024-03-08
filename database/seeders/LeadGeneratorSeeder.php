<?php

namespace Database\Seeders;

use App\Models\LeadGenerator;
use Illuminate\Database\Seeder;

class LeadGeneratorSeeder extends Seeder
{
    protected int $total = 10;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 1; $i < $this->total; $i++) {
            LeadGenerator::firstOrCreate([
                'name' => "Lead Generator $i",
                'sender_id' => "Lead Gen $i"
            ]);
        }
    }
}
