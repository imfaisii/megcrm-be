<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\Surveyor;

class DeleteSurveyorAction extends AbstractDeleteAction
{
    protected string $modelClass = Surveyor::class;
}
