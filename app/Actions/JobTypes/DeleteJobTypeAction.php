<?php

namespace App\Actions\JobTypes;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\JobType;

class DeleteJobTypeAction extends AbstractDeleteAction
{
    protected string $modelClass = JobType::class;
}
