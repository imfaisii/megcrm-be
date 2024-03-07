<?php

namespace Database\Seeders;

use App\Models\CallCenterStatus;
use Illuminate\Database\Seeder;

class CallCenterStatusSeeder extends Seeder
{
    protected $entries = [
        'Condensing Boiler',
        'No Answers',
        'Survey Booked',
        'Not Interested',
        'Called x3',
        'Private Tenant',
        'No benefits',
        'Oil Boiler',
        'ASHP',
        'Survey Done',
        'Ireland',
        'Waiting for boiler pick',
        'Call again next week',
        'Cravan',
        'Called back 2 weeks',
        'Called twice no answer',
        'Call again in 3 months',
        'Waiting for dm',
        'Child Benefit income',
        'Unmatched verified',
        'Call again in evening',
        'Already have a booking',
        'Call back tomorrow',
        'Repeat',
        'Park Home',
        'Council House',
        'Northern Ireland',
        'Incorrect Number',
        'Call me back later',
        'I will call you',
        'With another company',
        'Combi Condensing',
        'Cancelled',
        'Sent Text|Email',
        'Waiting documents',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $callCenterStatus) {
            CallCenterStatus::firstOrCreate([
                'name' => $callCenterStatus,
                'created_by_id' => 1,
            ]);
        }
    }
}
