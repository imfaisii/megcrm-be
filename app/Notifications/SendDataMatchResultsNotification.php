<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Twilio\TwilioChannel;
use NotificationChannels\Twilio\TwilioSmsMessage;

class SendDataMatchResultsNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 2;

    public $timeout = 600;   // as it has to deal with 30 contacts creation plus some time break for timeout scenario


    /**
     * Create a new notification instance.
     */
    public function __construct(public array $data)
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
        $channels = $notifiable->routes;

        $viaChannel = [];
        if ($channels['mail']) {
            $viaChannel[] = 'email';
        }
        if ($channels['twilio']) {
            $viaChannel[] = TwilioChannel::class;
        }

        return app()->isLocal() ? ['mail'] : $viaChannel;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $object = (new MailMessage)
            ->level("success")
            ->replyTo(config('credentials.DataMatchSupportEmail'))
            ->cc(data_get($this->data, '0.leadGen_cc_email', []))
            ->subject("MEG DataMatch Results")
            ->greeting("Hi " . data_get($this->data, '0.leadGen_name', 'Our Valuable User') . ",")
            ->line('Meg has received the following Data Match Results against your jobs.');
        foreach ($this->data as $key => $value) {
            $message = "The Data Match Result For {$value['first_name']} {$value['last_name']} with DOB {$value['dob']} for address {$value['address']} {$value['post_code']} got the result as {$value['datamatch_progress']} that was processed on {$value['datamatch_progress_date']}. ";
            $object->line($message);
        }
        $object->line('Thank you for using our application!');
        return $object;
    }

    public function toTwilio(object $notifiable)
    {
        $message = "Hi " . data_get($this->data, '0.leadGen_name', 'Our Valuable User') . ",\n";
        foreach ($this->data as $key => $value) {
            $message .= "The Data Match Result For {$value['first_name']} {$value['last_name']} with DOB {$value['dob']} for address {$value['address']} {$value['post_code']} got the result as {$value['datamatch_progress']} as processed on {$value['datamatch_progress_date']}. \n";
        }
        $message .= "\n";
        $message .= "Thank you for using our application! \n";
        $message .= "Regards, \n";
        $message .= "MEG";
        return (new TwilioSmsMessage())->content($message)->from(
            "MEG"
        );
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
