<?php

namespace App\Actions\CallCenters\CallCenterStatuses;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\CallCenterStatus;

class UpdateCallCenterStatusAction extends AbstractUpdateAction
{
    protected string $modelClass = CallCenterStatus::class;
}
