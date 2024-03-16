<?php

namespace App\Console\Commands\OneTime;

use App\Models\Lead;
use Exception;
use Illuminate\Console\Command;

use function App\Helpers\generateUniqueRandomString;

class AddReferenceNumberToLeads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-reference-number-to-leads';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This commands add the reference number to each lead ';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info("Running Command");
            Lead::lazyById(1000, $column = 'id')
                ->each(function ($lead) {
                    $lead->update(['reference_number' => generateUniqueRandomString()]);
                });

            return 0;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
