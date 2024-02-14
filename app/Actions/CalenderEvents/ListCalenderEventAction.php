<?php

namespace App\Actions\CalendereEvent;

use App\Actions\Common\AbstractListAction;
use App\Models\CalenderEvents;

class ListCalenderEventAction extends AbstractListAction
{
    protected string $modelClass = CalenderEvents::class;
}
