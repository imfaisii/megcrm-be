<?php

namespace App\Actions\Calendars;

use App\Actions\Common\AbstractCreateAction;
use App\Models\Calendar;

class StoreCalendarAction extends AbstractCreateAction
{
    protected string $modelClass = Calendar::class;
}
