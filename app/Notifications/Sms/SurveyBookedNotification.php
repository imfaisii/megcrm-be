<?php

namespace App\Notifications\Sms;

use App\Enums\Events\SurveyBookedEnum;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\ExpoPushNotifications\ExpoChannel;
use NotificationChannels\ExpoPushNotifications\ExpoMessage;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SurveyBookedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    public function via(object $notifiable): array
    {
        return [TwilioChannel::class, ExpoChannel::class, 'database'];
    }

    public function toExpoPush($notifiable)
    {
        $lead = $notifiable->load(['surveyBooking', 'leadGenerator']);
        $surveyAt = Carbon::parse($lead->surveyBooking->survey_at)->format('l jS \of F Y h:i A');

        return ExpoMessage::create()
            ->badge(1)
            ->enableSound()
            ->title("Survey Booked")
            ->body("{$lead['full_name']}'s ({$lead['post_code']}) survey is booked at: {$surveyAt}.");
    }

    public function toTwilio(object $notifiable)
    {
        $lead = $notifiable->load(['surveyBooking', 'leadGenerator']);

        $content = SurveyBookedEnum::getTwilioMessage(
            $lead->toArray(),
            Carbon::parse($lead->surveyBooking->survey_at)->format('l jS \of F Y h:i A') . ' - ' . Carbon::parse($lead->surveyBooking->survey_to)->format('h:i A'),
            $lead->leadGenerator->sender_id
        );

        return (new TwilioSmsMessage())->content($content)->from(
            app()->isLocal()
                ? config('services.twilio.number')
                : $lead->leadGenerator->sender_id
        );
    }

    public function toDatabase(object $notifiable)
    {
        $lead = $notifiable->load(['surveyBooking', 'leadGenerator']);

        $content = SurveyBookedEnum::getTwilioMessage(
            $lead->toArray(),
            Carbon::parse($lead->surveyBooking->survey_at)->format('l jS \of F Y h:i A') . ' - ' . Carbon::parse($lead->surveyBooking->survey_to)->format('h:i A'),
            $lead->leadGenerator->sender_id
        );

        return [
            'title' => 'Survey SMS Sent',
            'content' => $content
        ];
    }
}
