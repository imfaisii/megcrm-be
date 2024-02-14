<?php

namespace App\Helpers;

use App\Actions\Common\BaseModel;
use App\Notifications\TestNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Notification;

class NotificationHelper
{
  /**
   * Send a database notification to different notifications via database drivers 
   *
   * @param BaseModel|Collection|null $model The model or collection of models to send the notification to
   * @param array $NotificationData The data to pass to the notification its an array of array having data at first index and notification at second 
   * @param mixed ...$extra_data Any additional data to pass to the notification
   */
  public static function sendDBNotification(null|BaseModel|Collection $model, array $NotificationData, ...$extra_data)
  {
    // $data = [
    //   [
    //     'notificationClass' => TestNotification::class,
    //     'notificationData' => [
    //       'name' => 'TestNotification',
    //       'data' => 'test',
    //     ]
    //   ]
    // ];
    if (is_null($model)) {
      return false;
    }
    foreach ($NotificationData as $eachNotification) {

      try {
        Notification::send($model, app()->make(data_get($eachNotification, 'notificationClass'), ['via' => ['database','slack'], 'data' => [...data_get($eachNotification, 'notificationData'), ...$extra_data]]));
      } catch (\Throwable $th) {
      }
    }
  }
}
