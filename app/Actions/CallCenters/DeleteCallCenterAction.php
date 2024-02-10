<?php

namespace App\Actions\CallCenters;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\CallCenter;

class DeleteCallCenterAction extends AbstractDeleteAction
{
    protected string $modelClass = CallCenter::class;
}
