<?php

namespace App\Observers\Leads;

use App\Enums\Events\SurveyBookedEnum;
use App\Models\CalenderEvent;
use App\Models\SurveyBooking;
use App\Services\TwilioService;
use Carbon\Carbon;
use Exception;
use Log;

class SurveyBookingObserver
{
    public function notification(SurveyBooking $surveyBooking)
    {
        $updatedProperties = $surveyBooking->getDirty();

        if (
            array_key_exists('surveyor_id', $updatedProperties)
            || array_key_exists('survey_at', $updatedProperties)
            || array_key_exists('survey_to', $updatedProperties)
            || array_key_exists('preffered_time', $updatedProperties)
        ) {

            if ($surveyBooking->survey_at && $surveyBooking->surveyor_id) {
                $surveyBooking->load(['user', 'lead.leadGenerator']);
                $prefferedTime = ($surveyBooking->preffered_time ? "( {$surveyBooking->preffered_time} )" : '');

                $title = SurveyBookedEnum::TITLE . ' with ' . $surveyBooking->lead->full_name . $prefferedTime;
                $surveyAt = $surveyBooking->survey_at;
                $surveyTo = $surveyBooking->survey_to;

                $time = Carbon::parse($surveyAt)->format(config('app.date_time_format')) . ' - ' . Carbon::parse($surveyTo)->format(config('app.date_time_format'));

                $surveyBooking->lead->setStatus('Survey Booked', 'Assigned by system.');

                // try {

                //     $twilioService = new TwilioService($surveyBooking->lead->leadGenerator->sender_id);

                //     if ($surveyBooking->lead->phone_number_formatted) {
                //         $twilioService->message(
                //             $surveyBooking->lead->phone_number_formatted,
                //             SurveyBookedEnum::getTwilioMessage(
                //                 $surveyBooking->lead->full_name,
                //                 $time,
                //                 $surveyBooking->lead->leadGenerator->name
                //             ),
                //         );
                //     }
                // } catch (Exception $e) {
                //     Log::channel('twilio')->error("Failed to send message on: {$surveyBooking->lead->phone_number_formatted}. {$e->getMessage()}");
                // }

                $surveyBooking->lead->update([
                    'is_marked_as_job' => true,
                ]);

                CalenderEvent::updateOrCreate(
                    [
                        'user_id' => $surveyBooking->user->id,
                        'calendar_id' => SurveyBookedEnum::getCalendarId(),
                        'eventable_type' => SurveyBooking::class,
                        'eventable_id' => $surveyBooking->id,
                    ],
                    [
                        'title' => $title,
                        'start_date' => Carbon::parse($surveyAt),
                        'end_date' => Carbon::parse($surveyTo),
                        'all_day' => SurveyBookedEnum::IS_FULL_DAY,
                        'description' => SurveyBookedEnum::getDescriptionMessage($surveyBooking->lead->full_name, $surveyBooking->lead->address, $time),
                        'notification' => [
                            'title' => SurveyBookedEnum::NOTIFICATION_TITLE,
                            'subtitle' => SurveyBookedEnum::getDescriptionMessage($surveyBooking->lead->full_name, $surveyBooking->lead->address, $time),
                            'module' => 'surveys',
                            'link' => '/calendar',
                        ],
                        'created_by_id' => auth()->user(),
                    ]
                );
            }
        }
    }

    public function created(SurveyBooking $surveyBooking): void
    {
        $this->notification($surveyBooking);
    }

    public function updated(SurveyBooking $surveyBooking): void
    {
        $this->notification($surveyBooking);
    }
}
