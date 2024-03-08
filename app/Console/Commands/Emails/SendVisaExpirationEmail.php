<?php

namespace App\Console\Commands\Emails;

use App\Jobs\Emails\VisaExpirationEmail;
use Illuminate\Console\Command;

class SendVisaExpirationEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-visa-expiration-emails';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends visa expiration email alerts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        (new VisaExpirationEmail)->dispatch();
    }
}
