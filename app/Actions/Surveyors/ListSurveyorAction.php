<?php

namespace App\Actions\Surveyors;

use App\Actions\Common\AbstractListAction;
use App\Models\Surveyor;

class ListSurveyorAction extends AbstractListAction
{
    protected string $modelClass = Surveyor::class;
}
