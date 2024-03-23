<?php

namespace App\Console\Commands\OneTime;

use App\Classes\GetAddress;
use App\Models\Lead;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FixLeadPostCodeAndAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-lead-post-code-and-address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is responsible for Fixing the post code and address of leads table in one format ';

    public function handle()
    {
        $this->info('Starting to Fix Lead`s Post Code And Address ...');
        $this->newLine();

        try {
            $getAddress = new GetAddress();
            $leads = Lead::all();

            $this->withProgressBar($leads, function ($lead) use ($getAddress) {
                $this->newLine();
                $this->info('DB:: post_code => ' . $lead->post_code . ', address => ' . $lead->address);

                [$postCode, $address, $plainAddres, $city, $county, $country, $buildingNumber, $subBuilding,$RawApiResponse,$actualPostCode] = $getAddress->adressionApi($lead->post_code, $lead->address);

                $this->info('API:: post code => ' . $postCode . ', address => ' . $address);

                $lead->update([
                    'post_code' => $postCode,
                    'address' => $address,
                    'plain_address' => $plainAddres,
                    'county' => $county,
                    'city' => $city,
                    'country' => $country,
                    'building_number' => $buildingNumber,
                    'sub_building' => $subBuilding,
                    'raw_api_response' => $RawApiResponse,
                    'actual_post_code' => $actualPostCode
                ]);

                Log::channel('addresso_api')
                    ->info("SUCCESS old:: {$lead->address} and new:: $address");
            });
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    // public function old()
    // {
    //     try {
    //         $admin = User::find(1);
    //         $AddressAPIObject = new GetAddress();
    //         $this->info('Starting to Fix Lead`s Post Code And Address ...');
    //         $this->newLine();
    //         $users = $this->withProgressBar(Lead::whereNull('file_address')->get(), function ($eachLead) use ($AddressAPIObject, $admin) {
    //             $this->newLine(1);
    //             $this->info("Looking for Lead with post code " . $eachLead->post_code . " with  address " . $eachLead->address);
    //             $oldAddress = $eachLead->address;
    //             $address = $AddressAPIObject->getCompleteAddress(address: $eachLead->address, postCode: $eachLead->post_code);
    //             if (blank($address)) {
    //                 $this->info("No address found for post code " . $eachLead->post_code . " with  address " . $eachLead->address . " with id : {$eachLead->id}");
    //                 $this->error("lagta server n pkr lia ");
    //                 // sleep(65);
    //                 $address = $AddressAPIObject->getCompleteAddress(address: $eachLead->address, postCode: $eachLead->post_code);
    //             }
    //             $eachLead->update([
    //                 'post_code' => $eachLead->post_code,
    //                 'address' => $address ?: $eachLead->address,
    //                 'file_address' => $oldAddress,
    //             ]);
    //         });
    //     } catch (Exception $e) {
    //         Log::driver('slack-crm')->error('Error: ' . $e->getMessage());
    //         return 1;
    //     }
    // }
}
