<?php

namespace App\Actions\CalenderEvents;

use App\Actions\Common\AbstractListAction;
use App\Models\CalenderEvent;

class ListCalenderEventAction extends AbstractListAction
{
    protected string $modelClass = CalenderEvent::class;
}
