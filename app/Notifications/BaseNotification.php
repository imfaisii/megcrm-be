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
use Illuminate\Support\Arr;

abstract class BaseNotification extends Notification implements ShouldQueue
{
  use Queueable;
  public array $data;
  public array $viaOptions;

  /**
   * Create a new notification instance.
   */
  public function __construct(...$params)
  {
    $this->data = Arr::get($params, 'data', []);
    $this->viaOptions = Arr::get($params, 'via', ['database']);
    $this->afterCommit();
    $this->onConnection(app()->isLocal() ? 'sync' : env('QUEUE_CONNECTION', 'database')); //later we would change it to redis
  }

  public function viaQueues(): array
  {
    return [
      'mail' => 'mail-queue',
      'slack' => 'slack-queue',
    ];
  }
  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return ($notifiable instanceof User) ? ($notifiable?->prefer_notification   ? json_decode($notifiable->prefer_notification) : $this->viaOptions) : ['mail'];
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    return $this->data;
  }


  //   public function toSlack($notifiable)
  //   {
  //     return (new SlackMessage)
  //  ->content("A new Update Status Message has been sent to the User {$notifiable->email}");
  //   }
}
