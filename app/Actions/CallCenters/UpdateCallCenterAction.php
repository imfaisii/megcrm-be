<?php

namespace App\Actions\CallCenters;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\CallCenter;

class UpdateCallCenterAction extends AbstractUpdateAction
{
    protected string $modelClass = CallCenter::class;
}
