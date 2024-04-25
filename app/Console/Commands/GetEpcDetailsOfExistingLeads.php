<?php

namespace App\Console\Commands;

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
        try {
            $this->info("Running Command...");

            Lead::whereNull('epc_details')->lazyById(1000, $column = 'id')
                ->each(function ($lead) {
                    dispatch(new GetEpcScrappedDataOfLead($lead));
                });

            $this->info("Success.");
            return 0;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
