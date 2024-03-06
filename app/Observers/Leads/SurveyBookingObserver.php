<?php

namespace App\Observers\Leads;

use App\Enums\Events\SurveyBookedEnum;
use App\Models\CalenderEvent;
use App\Models\SurveyBooking;
use Carbon\Carbon;

class SurveyBookingObserver
{
    public function notification(SurveyBooking $surveyBooking)
    {
        $updatedProperties = $surveyBooking->getDirty();

        if (
        true
            //array_key_exists('surveyor_id', $updatedProperties)
            //|| array_key_exists('survey_at', $updatedProperties)
           // || array_key_exists('survey_to', $updatedProperties)
          //  || array_key_exists('preffered_time', $updatedProperties)
        ) {

            if ($surveyBooking->survey_at && $surveyBooking->surveyor_id) {

                $surveyBooking->lead->setStatus('Survey Booked', 'Assigned by system.');
                $surveyBooking->lead->update([
                    'is_marked_as_job' => true,
                ]);

                $surveyBooking->load(['user', 'lead']);
                $prefferedTime = ($surveyBooking->preffered_time ? "( {$surveyBooking->preffered_time} )" : '');

                $title = SurveyBookedEnum::TITLE . ' with ' . $surveyBooking->lead->full_name . $prefferedTime;
                $surveyAt = $surveyBooking->survey_at;
                $surveyTo = $surveyBooking->survey_to;

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
                        'description' => SurveyBookedEnum::getDescriptionMessage($surveyBooking->lead->full_name, $surveyBooking->lead->address, Carbon::parse($surveyAt)->format(config('app.date_time_format')) . ' - ' . Carbon::parse($surveyTo)->format(config('app.date_time_format'))),
                        'notification' => [
                            'title' => SurveyBookedEnum::NOTIFICATION_TITLE,
                            'subtitle' => SurveyBookedEnum::getDescriptionMessage($surveyBooking->lead->full_name, $surveyBooking->lead->address, Carbon::parse($surveyAt)->format(config('app.date_time_format')) . ' - ' . Carbon::parse($surveyTo)->format(config('app.date_time_format'))),
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
