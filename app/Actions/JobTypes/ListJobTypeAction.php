<?php

namespace App\Actions\JobTypes;

use App\Actions\Common\AbstractListAction;
use App\Models\JobType;

class ListJobTypeAction extends AbstractListAction
{
    protected string $modelClass = JobType::class;
}
