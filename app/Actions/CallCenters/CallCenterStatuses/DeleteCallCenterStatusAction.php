<?php

namespace App\Actions\CallCenters\CallCenterStatuses;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\CallCenterStatus;

class DeleteCallCenterStatusAction extends AbstractDeleteAction
{
    protected string $modelClass = CallCenterStatus::class;
}
