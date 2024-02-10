<?php

namespace App\Notifications;

use App\Enums\AppEnum;
use App\Models\User;
use Hamcrest\Core\IsInstanceOf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

abstract class BaseNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(...$params)
  {
    $this->afterCommit();
    $this->onConnection(app()->isLocal() ? 'sync' : 'database'); //later we would change it to redis
  }

  public function viaQueues(): array
  {
    return [
      'mail' => AppEnum::MailQue,
      'slack' => AppEnum::SlackQue,
    ];
  }




  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    //this notifiable is an entry of user to whom the notification is being sent as one model each time 
    return ($notifiable instanceof User) ? ($notifiable?->prefer_notification   ? json_decode($notifiable->prefer_notification) : ['mail', ]) : ['mail'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable): MailMessage|Mailable
  {
    return   (new MailMessage)->view(['mails.status.status-update-email', 'mails.status.status-update-email'], ['data'=>['email' => 'hamza@gmail.com','password'=>'12324']]
  );
    return (new MailMessage)
      ->line('The introduction to the notification.')
      ->action('Notification Action', url('/'))
      ->line('Thank you for using our application!');
      // ->attach(storage_path('app/public/dummy.pdf'));
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


  public function toSlack($notifiable)
  {
    return (new SlackMessage)
      ->content("A new Update Status Message has been sent to the User {$notifiable->email}");
  }
}
