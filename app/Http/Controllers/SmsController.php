<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sms\SendSmsRequest;
use App\Models\Lead;
use App\Notifications\Sms\TwilioMessageNotification;

class SmsController extends Controller
{
    public function sendSmsToLead(SendSmsRequest $request, Lead $lead)
    {
        $lead->notify(new TwilioMessageNotification($request->body));

        return $this->success();
    }
}
