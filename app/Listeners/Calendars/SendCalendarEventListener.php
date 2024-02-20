<?php

namespace App\Listeners\Calendars;

use App\Events\Calendars\NewCalendarEvent;
use App\Notifications\Events\CalendarEventNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendCalendarEventListener implements ShouldQueue
{
    public function handle(NewCalendarEvent $event): void
    {
        if ($event->calenderEvent?->notification) {
            $event
                ->calenderEvent
                ->user
                ->notify(new CalendarEventNotification($event->calenderEvent->notification));
        }
    }
}
