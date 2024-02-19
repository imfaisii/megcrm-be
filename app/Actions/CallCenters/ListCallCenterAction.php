<?php

namespace App\Actions\CallCenters;

use App\Actions\Common\AbstractListAction;
use App\Models\CallCenter;

class ListCallCenterAction extends AbstractListAction
{
    protected string $modelClass = CallCenter::class;
}
