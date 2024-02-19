<?php

namespace App\Actions\Calendars;

use App\Actions\Common\AbstractListAction;
use App\Models\Calendar;

class ListCalendarAction extends AbstractListAction
{
    protected string $modelClass = Calendar::class;
}
