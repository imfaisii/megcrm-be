<?php

namespace App\Jobs;

use App\Cache\AirCallContactCreationCache;
use App\Classes\GetAddress;
use App\Fascade\AirCallFascade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

use function App\Helpers\fixNumberForAirCall;

class AircallContactCreationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public array $leads, )
    {
        //
    }
    public $timeout = 54000;   // as it has to deal with 30 contacts creation plus some time break for timeout scenario

    public $tries = 2;

    public $backoff = 5;



    /**
     * Execute the job.
     */
    public function handle(AirCallContactCreationCache $cacheObj): void
    {
        foreach ($this->leads as $key => $eachLead) {
            if ($cacheObj->getData($eachLead['id']))
                continue;
            $response = AirCallFascade::createContact([
                "first_name" => $eachLead['first_name'] ?? 'Mr',
                "last_name" => $eachLead['last_name'] ?? '',
                "information" => "leadId:{$eachLead['id']}",
                "phone_numbers" => [
                    [
                        "label" => "Home",
                        "value" => fixNumberForAirCall($eachLead['phone_no']),
                    ]
                ],
                "emails" => [
                    [
                        "label" => "Personal",
                        "value" => $eachLead['email']
                    ]
                ]
            ]);
            if ($response->status() == 200) {
                $cacheObj->setData($eachLead['id'], true, );
            }

        }
    }

    public function failed(?Throwable $exception): void
    {
        Log::channel('slack-crm')->info("Aircall Contact creation failed with exception '$exception' ");
    }
}
