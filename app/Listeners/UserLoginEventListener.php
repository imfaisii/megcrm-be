<?php

namespace App\Listeners;

use App\Cache\AirCallContactCreationCache;
use App\Cache\TwoFactorCodeCacheClass;
use App\Events\LoginUserEvent;
use App\Models\User;
use App\Notifications\SendOtpNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use function App\Helpers\generateUniqueRandomString;

class UserLoginEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(LoginUserEvent $event): void
    {
        try {
            $cacheObj = new TwoFactorCodeCacheClass();
            $code = $cacheObj->setData($event?->User?->id, generateUniqueRandomString(6), TwoFactorCodeCacheClass::cacheTime);
            $code = $cacheObj->getData($event?->User?->id);
            
            $event?->User->notify(new SendOtpNotification(['code' => $code, 'expiration_time' => TwoFactorCodeCacheClass::cacheTime]));

        } catch (Exception $e) {
            Log::driver('slack-crm')->error('File : ' . __FILE__ . ', Function: ' . __FUNCTION__ . 'Error: ' . $e->getMessage());
        }
    }
}
