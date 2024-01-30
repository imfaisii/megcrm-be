<?php

namespace Database\Seeders;

use App\Models\LeadStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LeadStatusSeeder extends Seeder
{
    protected $entries = [
        'Raw Lead',
        'Ready for Survey',
        'Surveyed',
        'Ready for Installation',
        'Waiting for Datamatch',
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
        'Cancelled'
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
                'created_by_id' => 1
            ]);
        }
    }
}
