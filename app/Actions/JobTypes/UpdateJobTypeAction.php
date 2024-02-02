<?php

namespace App\Actions\JobTypes;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\JobType;

class UpdateJobTypeAction extends AbstractUpdateAction
{
    protected string $modelClass = JobType::class;
}
