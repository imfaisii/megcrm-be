<?php

namespace App\Actions\CallCenters;

use App\Actions\Common\AbstractCreateAction;
use App\Models\CallCenter;

class StoreCallCenterAction extends AbstractCreateAction
{
    protected string $modelClass = CallCenter::class;
}
