<?php

namespace Database\Seeders;

use App\Models\JobType;
use Illuminate\Database\Seeder;

class JobTypeSeeder extends Seeder
{
    protected array $entries = [
        'LAD',
        'ECO4',
        'GBIS',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $jobType) {
            JobType::firstOrCreate([
                'name' => $jobType,
            ]);
        }
    }
}
