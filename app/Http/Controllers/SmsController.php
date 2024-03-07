<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sms\SendSmsRequest;
use App\Models\Lead;
use App\Services\TwilioService;

class SmsController extends Controller
{
    public function sendSmsToLead(SendSmsRequest $request, Lead $lead)
    {
        $twilioService = new TwilioService(
            app()->isLocal()
                ? config('services.twilio.number')
                : $lead->leadGenerator->sender_id
        );
        
        $twilioService->message(
            $lead->phone_number_formatted,
            $request->body
        );

        return $this->success();
    }
}
