<?php

namespace App\Actions\CallCenters\CallCenterStatuses;

use App\Actions\Common\AbstractCreateAction;
use App\Models\CallCenterStatus;

class StoreCallCenterStatusAction extends AbstractCreateAction
{
    protected string $modelClass = CallCenterStatus::class;
}
