<?php

namespace App\Http\Controllers;

use App\Http\Requests\Sms\SendSmsRequest;
use App\Models\Lead;
use App\Models\SmsTemplate;
use App\Notifications\Sms\TwilioMessageNotification;
use Exception;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function sendSmsToLead(SendSmsRequest $request, Lead $lead)
    {
        $lead->notify(new TwilioMessageNotification($request->body));

        return $this->success();
    }

    public function sendTrackingLinkToLead(Request $request, Lead $lead)
    {
        $link = $lead->getEmailTrackingLink($lead);
        $lead->update(['tracking_link' => $link]);
        $body = "Hi {$lead['first_name']},\n\nPlease click the link below to upload required documents:\n\n{$link}.\n\nRegards,";
        $lead->notify(new TwilioMessageNotification($body));

        return $this->success(data: [
            'link' => $link
        ]);
    }

    public function sendSmsWithTemplate(Request $request, Lead $lead, SmsTemplate $smsTemplate)
    {
        try {
            $lead->load('leadGenerator');

            $valuesToBeFilled = [];
            $fields = $smsTemplate->properties;
            $data = [
                'name' => $lead->first_name ?? 'name',
                'email' =>  $lead->leadGenerator->email ?? 'email',
                'whatsapp' => $lead->leadGenerator->phone_no ?? 'phone',
                'address' => $lead->address ?? 'address',
                'company_name' => $lead->leadGenerator->name ?? 'company_name',
                'phone' => $lead->leadGenerator->phone_no ?? 'phone'
            ];


            foreach ($fields as $key => $value) {
                $valuesToBeFilled[$value] = $data[$value] ?? $value;
            }

            foreach ($valuesToBeFilled as $key => $value) {
                $smsTemplate->body = str_replace("{{$key}}", $value, $smsTemplate->body);
            }

            $lead->notify(new TwilioMessageNotification($smsTemplate->body));


            return $this->success();
        } catch (Exception $exception) {
            //
        }
    }
}
