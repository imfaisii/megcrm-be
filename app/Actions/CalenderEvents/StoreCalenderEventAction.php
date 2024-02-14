<?php

namespace App\Actions\CalenderEvents;

use App\Actions\Common\AbstractCreateAction;
use App\Models\CalenderEvent;

class StoreCalenderEventAction extends AbstractCreateAction
{
    protected string $modelClass = CalenderEvent::class;
}
