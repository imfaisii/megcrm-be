<?php

namespace App\Actions\CallCenters\CallCenterStatuses;

use App\Actions\Common\AbstractListAction;
use App\Models\CallCenterStatus;

class ListCallCenterStatusAction extends AbstractListAction
{
    protected string $modelClass = CallCenterStatus::class;
}
