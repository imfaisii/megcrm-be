<?php

namespace App\Console\Commands\OneTime;

use App\Enums\Permissions\RoleEnum;
use App\Fascade\AirCallFascade;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AssignUserIdsOfAirCallToUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:assign-user-ids-of-air-call-to-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is responsible for assigning user ids of air calls to users';

    protected $help = "For this command to work, your email must match with the email of the air call";


    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $this->info('Starting to assign user ids of air calls to users...');
            $getUsers = AirCallFascade::getUsers();  // fetch all the users
            $this->newLine();
            $users = $this->withProgressBar(data_get($getUsers->getOriginalContent(), 'data', collect([])), function ($eachuser) {
                $this->newLine(1);
                $this->info("Looking for {$eachuser['email']}...");

                $user = User::query()->whereHas('roles', function ($query) {
                    return $query->whereIn('name', [RoleEnum::SUPER_ADMIN, RoleEnum::CSR]);
                })->where('aircall_email_address', $eachuser['email'])->first();
                if ($user) {
                    $this->info("Found User: {$user->email} with ID: {$user->id}...");
                    $this->info("Getting Assigned Numbers");
                    $getNumber = AirCallFascade::getUsers(userId:$eachuser['id']);
                    $number = $getNumber->getData(true);

                    $numberArray = data_get($number, 'data.0.numbers.*.id');
                    tap($user)->update([
                        'air_caller_id' => $eachuser['id'],
                        'phone_number_aircall' => $numberArray,
                    ]);
                }
            });
        } catch (Exception $e) {
            Log::driver('slack-crm')->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
