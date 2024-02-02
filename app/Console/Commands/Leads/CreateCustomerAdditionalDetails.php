<?php

namespace App\Console\Commands\Leads;

use App\Models\Lead;
use App\Models\LeadCustomerAdditionalDetail;
use Illuminate\Console\Command;

class CreateCustomerAdditionalDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'create-lead-additional:customer';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command created additional details for lead.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leads = Lead::all();

        foreach ($leads as $key => $lead) {
            LeadCustomerAdditionalDetail::firstOrCreate([
                'lead_id' => $lead->id
            ]);
        }
    }
}
