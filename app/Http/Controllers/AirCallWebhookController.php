<?php

namespace App\Http\Controllers;

use App\Actions\Aircall\AirCallWebhookAction;
use App\Enums\Aircall\AirCallEnum;
use App\traits\Jsonify;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AirCallWebhookController extends Controller
{
    use Jsonify;
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, AirCallWebhookAction $action)
    {
        //Currently we are only Logging the incoming request to Postman
        try {
            throw_unless($this->verifyToken($request->all()), new Exception('The request is not from AirCall'));
            $data = $request->all();
            switch (Arr::get($data, 'resource')) {
                case AirCallEnum::NUMBER_RESOURCE:
                    $action->LogEvent($data);
                    break;
                case AirCallEnum::USER_RESOURCE:
                    $action->LogEvent($data);
                    break;
                case AirCallEnum::CALL_RESOURCE:
                    $action->LogEvent($data);
                    break;
                case AirCallEnum::CONTACT_RESOURCE:
                    $action->LogEvent($data);
                    break;
                default:
                    throw new Exception("no such resource");
                    break;
            }
        } catch (Exception $e) {
            Log::driver('slack-meg-crm-webhook')->error($e->getMessage(), $request->all());  // Log event to Slack
            return $this->error($e->getMessage());
        }
        return $this->success();  // it must return 200 status code to air call server else the webhook will be disbaled by the air call 
    }


    public function verifyToken(array $data): bool
    {
        try {
            return Arr::get($data, 'token', null) == config('credentials.AIRCALL_WEBHOOK_TOKEN', 'testToken');
        } catch (Exception $e) {
            return false;
        }
    }
}
