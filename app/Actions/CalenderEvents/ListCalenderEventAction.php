<?php

namespace App\Actions\CalendereEvent;

use App\Actions\Common\AbstractListAction;
use App\Models\CalenderEvent;

class ListCalenderEventAction extends AbstractListAction
{
    protected string $modelClass = CalenderEvent::class;
}
