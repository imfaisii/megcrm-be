<?php

namespace App\Observers\Calendars;

use App\Events\Calendars\NewCalendarEvent;
use App\Models\CalenderEvent;
use Exception;

class CalendarEventObserver
{
    public function notification(CalenderEvent $calenderEvent)
    {
        if (! is_null($calenderEvent->notification)) {
            try {
                event(new NewCalendarEvent($calenderEvent));
            } catch (Exception $e) {
                //
            }
        }
    }

    public function created(CalenderEvent $calenderEvent): void
    {
        $this->notification($calenderEvent);
    }

    public function updated(CalenderEvent $calenderEvent): void
    {
        $this->notification($calenderEvent);
    }
}
