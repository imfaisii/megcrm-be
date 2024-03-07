<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use Illuminate\Database\Seeder;

class LeadStatusSeeder extends Seeder
{
    protected $entries = [
        'Raw Lead',
        'Ready for Survey',
        'Survey Done',
        'Waiting for Datamatch',
        'Ready for Installation',
        'Installed',
        'All Leads',
        'Survey Booked',
        'Survey In Progress',
        'Awaiting EPR',
        'Awaiting Pre-Checking',
        'Awaiting Review',
        'Awaiting Install Date',
        'Install Booked',
        'Installation In Progress',
        'Installed',
        'Partial Project',
        'Partial Project- Completed',
        'Ready for Submission',
        'Ready To Offload',
        'Job Submitted',
        'Remedial',
        'Ready For Scaffolding (Pre Checked)',
        'Scaffolding Booked (Order Material)',
        'Material Ordered',
        'Follow Up',
        'Install Boiler Pending Quee',
        'Partial Installation In Progress',
        'SC -New Job',
        'SC- Job Processing',
        'Job Validated',
        'SC- Move To Submission',
        'Job Submitted - SC',
        'Job Submitted By Other Companies',
        'Awaiting Information',
        'Reschedule Jobs',
        'Cancelled Lead',
        'Not Interested',
        'Waiting for Boiler Picture',
        'Called from ring central',
        'Called from second number',
        'No answer',
        'Survey Pending',
        'Cancelled Survey',
        'Cancelled Job',
        'Condensing Boiler',
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->entries as $key => $leadStatus) {
            LeadStatus::firstOrCreate([
                'name' => $leadStatus,
                'color' => $key < count($this->entries) / 2 ? 'warning' : 'success',
                'created_by_id' => 1,
            ]);
        }
    }
}
