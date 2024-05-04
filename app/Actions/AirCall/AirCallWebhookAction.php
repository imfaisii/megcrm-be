<?php

namespace App\Actions\AirCall;

use App\Traits\Jsonify;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AirCallWebhookAction
{
    /** This class is all responsible for handling the incoming request  of aircalls  **/
    use Jsonify;

    public function LogEvent(array $data): void
    {
        try {
            Log::driver('slack-meg-crm-webhook')->info('Webhook received of ' . Arr::get($data, 'event'), Arr::get($data, 'data'));
        } catch (Exception $e) {
        }
    }
}
