<?php

namespace Database\Seeders;

use App\Models\Calendar;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    protected $entries = [
        [
            'name' => 'Scheduled Calls',
            'color' => 'info',
        ],
        [
            'name' => 'Surveys',
            'color' => 'success',
        ],
        [
            'name' => 'Installations',
            'color' => 'warning',
        ],
        [
            'name' => 'Complaints',
            'color' => 'error',
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $calendar) {
            Calendar::firstOrCreate([
                'name' => $calendar['name'],
                'color' => $calendar['color'],
                'created_by_id' => 1,
            ]);
        }
    }
}
