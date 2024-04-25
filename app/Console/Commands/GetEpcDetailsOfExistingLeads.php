<?php

namespace App\Console\Commands;

use App\Actions\Leads\GetOtherSitesLinkAction;
use App\Jobs\GetEpcScrappedDataOfLead;
use App\Models\Lead;
use Exception;
use Illuminate\Console\Command;

class GetEpcDetailsOfExistingLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:get-epc-details-of-existing-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command adds scrapped data of epc of existing leads.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 1;
        $leads =  Lead::whereNull('epc_details')->latest()
            ->get();
        $leadsCount = count($leads);

        $leads
            ->each(function ($lead) use (&$count, $leadsCount) {
                $this->info("Getting EPC of {$lead->post_code} {$count}/{$leadsCount} ID: {$lead->id}");
                (new GetOtherSitesLinkAction())->getEpcDetails($lead);
                $count++;
            });
    }
}
