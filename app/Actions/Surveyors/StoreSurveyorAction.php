<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractCreateAction;
use App\Models\Surveyor;

class StoreSurveyorAction extends AbstractCreateAction
{
    protected string $modelClass = Surveyor::class;
}
