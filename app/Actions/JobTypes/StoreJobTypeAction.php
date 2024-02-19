<?php

namespace App\Actions\JobTypes;

use App\Actions\Common\AbstractCreateAction;
use App\Models\JobType;

class StoreJobTypeAction extends AbstractCreateAction
{
    protected string $modelClass = JobType::class;
}
