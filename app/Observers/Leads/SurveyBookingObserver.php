<?php

namespace App\Observers\Leads;

use App\Enums\Events\SurveyBookedEnum;
use App\Models\CalenderEvent;
use App\Models\SurveyBooking;
use App\Notifications\MobileApp\ExpoNotification;
use App\Notifications\Sms\SurveyBookedNotification;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

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
            || array_key_exists('is_sms_alert_enabled', $updatedProperties)
        ) {

            if ($surveyBooking->survey_at && $surveyBooking->surveyor_id) {
                $surveyBooking->load(['user', 'lead.leadGenerator']);
                $prefferedTime = ($surveyBooking->preffered_time ? "( {$surveyBooking->preffered_time} )" : '');

                $title = SurveyBookedEnum::TITLE . ' with ' . $surveyBooking->lead->full_name . $prefferedTime;
                $surveyAt = $surveyBooking->survey_at;
                $surveyTo = $surveyBooking->survey_to;

                $time = Carbon::parse($surveyAt)->format(config('app.date_time_format')) . ' - ' . Carbon::parse($surveyTo)->format(config('app.date_time_format'));

                $surveyBooking->lead->setStatus('Survey Booked', 'Assigned by system.');
                $surveyBooking->lead->update([
                    'is_marked_as_job' => true,
                ]);


                try {
                    if ($surveyBooking->is_sms_alert_enabled) {

                        $surveyBooking
                            ->lead
                            ->notify(new SurveyBookedNotification());
                    }
                } catch (Exception $e) {
                    Log::driver('slack_exceptions')->error(json_encode([
                        'message' => $e->getMessage(),
                        'host' => request()->getHttpHost(),
                        'ip' => request()->ip(),
                    ]));

                    Log::channel('twilio')->error("Failed to send message on: {$surveyBooking->lead->phone_number_formatted}. {$e->getMessage()}");
                }

                // expo push notification
                $surveyTime = Carbon::parse($surveyAt)->format('F j, Y, g:i a');
                $surveyBooking->user->notify(new ExpoNotification(
                    "New survey assigned ({$surveyBooking->lead->actual_post_code})",
                    "Survey time: {$surveyTime}\nLocation: {$surveyBooking->lead->plain_address}"
                ));

                // calendar event
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
