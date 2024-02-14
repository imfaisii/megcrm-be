<?php

namespace App\Actions\CalendereEvent;

use App\Actions\Common\AbstractCreateAction;
use App\Models\CalenderEvent;

class StoreCalenderEventAction extends AbstractCreateAction
{
    protected string $modelClass = CalenderEvent::class;
}
