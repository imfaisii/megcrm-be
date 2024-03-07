<?php

namespace App\Console\Commands;

use App\Models\LeadGenerator;
use Illuminate\Console\Command;

class FixSenderIdInLeadGeneratorsTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-sender-id-in-lead-generators-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command adds sender id to existing lead generators.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        LeadGenerator::all()->each(function ($leadGenerator) {
            if (!$leadGenerator->sender_id && $leadGenerator->name) {
                $leadGenerator->update([
                    'sender_id' => substr($leadGenerator->name, 0, 11)
                ]);
            }
        });
    }
}
