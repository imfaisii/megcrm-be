<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\Surveyor;

class UpdateSurveyorAction extends AbstractUpdateAction
{
    protected string $modelClass = Surveyor::class;
}
