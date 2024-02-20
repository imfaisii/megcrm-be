<?php

namespace App\Events\Calendars;

use App\Models\CalenderEvent;

abstract class AbstractCalendarEvent
{
    public function __construct(
        public CalenderEvent $calenderEvent
    ) {
    }
}
