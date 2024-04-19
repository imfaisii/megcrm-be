<?php

namespace App\Actions\CustomerSupport;

use App\Actions\Common\AbstractCreateAction;
use App\Enums\AppEnum;
use App\Models\Lead;
use App\Notifications\CustomerSupportNotification;
use App\traits\Jsonify;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class CustomerSupportEmailAction extends AbstractCreateAction
{
    use Jsonify;
    public function sendEmail(array $data)
    {
        try {
            $lead = Lead::find($data['id']);
            activity()
                ->causedBy(null)
                ->performedOn($lead)
                ->withProperties([
                    'content' => $data['content'],
                ])
                ->event('support')
                ->log('The Customer has sent an support email');

            foreach (AppEnum::customerSupportEmails() as $key => $eachEmail) {
                try {

                    Notification::route('mail', [
                        $eachEmail => 'Support MEG',
                    ])
                        ->notify(new CustomerSupportNotification([
                            'lead_reference_number' => $lead->reference_number,
                            'content' => $data['content'],
                            'customer_name' => $lead->first_name . ' ' . $lead->last_name,
                            'post_code' => $lead->post_code,
                            'address' => $lead->address,
                            'lead_id'=>$lead->id
                        ]));
                } catch (Exception $e) {
                    Log::channel('slack_exceptions')->info('Exception found in sending mail ' . __FUNCTION__ . ' with message ' . $e->getMessage());
                }
            }
            return $this->success();


        } catch (\Throwable $e) {
            # code...
            $this->exception($e);
        }
    }


}
