<?php

namespace App\Notifications\Customer;

use App\Enums\AppEnum;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class CustomerLeadTrackingMail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $destinationUrl)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', TwilioChannel::class];
    }


    public function toTwilio(object $notifiable)
    {
        // $route = url(config('app.CUSTOMER_URL') . "/tracking/{$this->params['lead']}/{$this->params['signature']}" . '?expires=' . $this->params['expires'] . "&SignatureForDelete={$this->params['SignatureForDelete']}&SignatureForUpload={$this->params['SignatureForUpload']}&SignatureForData={$this->params['SignatureForData']}&SignatureForSupport={$this->params['SignatureForSupport']}&Model={$this->params['model']}");
        $lead = $notifiable->load(['leadGenerator']);
        $message = "Hi {$notifiable->title} {$notifiable->first_name} {$notifiable->last_name},\n\n";
        $message .= "We hope this email finds you well!\n\n";
        $message .= "We're excited to inform you that you've been granted exclusive access to review and update your details securely. Simply click the link below to access your personalized portal:\n";
        $message .= "$this->destinationUrl\n\n";
        $message .= "This link will provide you with a convenient platform to review your information and upload any necessary documents. Please ensure to complete this process by " . Carbon::now()->addDays(AppEnum::LEAD_TRACKNG_DAYS_ALLOWED)->format('Y-m-d H:i:s') . " as the link will expire after this time.\n\n";
        $message .= "Should you encounter any difficulties or have any questions, feel free to reach out to our dedicated support team.\n\n";
        $message .= "Thank you for choosing " . config('app.name') . ". We look forward to assisting you further.\n\n";
        $message .= "Sincerely,\n";
        return (new TwilioSmsMessage())->content($message)->from(
            app()->isLocal()
            ? config('services.twilio.number')
            : $lead->leadGenerator->sender_id
        );
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // $route = url(config('app.CUSTOMER_URL') . "/tracking/{$this->params['lead']}/{$this->params['signature']}" . '?expires=' . $this->params['expires'] . "&SignatureForDelete={$this->params['SignatureForDelete']}&SignatureForUpload={$this->params['SignatureForUpload']}&SignatureForData={$this->params['SignatureForData']}&SignatureForSupport={$this->params['SignatureForSupport']}&Model={$this->params['model']}");

        return (new MailMessage)
            ->greeting("Hi {$notifiable->title} {$notifiable->first_name} {$notifiable->last_name},")
            ->line('We hope this email finds you well!')
            ->line("We're excited to inform you that you've been granted exclusive access to review and update your details securely. Simply click the link below to access your personalized portal:")
            ->action('View', $this->destinationUrl)
            ->line('This link will provide you with a convenient platform to review your information and upload any necessary documents. Please ensure to complete this process by ' . Carbon::now()->addDays(AppEnum::LEAD_TRACKNG_DAYS_ALLOWED)->format('Y-m-d H:i:s') . ' as the link will expire after this time.')
            ->line('Should you encounter any difficulties or have any questions, feel free to reach out to our dedicated support team .')
            ->line('Thank you for choosing' . config('app.name') . ' We look forward to assisting you further')
            ->line('Sincerely,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
