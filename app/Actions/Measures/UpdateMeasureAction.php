<?php

namespace App\Actions\Measures;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\Measure;

class UpdateMeasureAction extends AbstractUpdateAction
{
    protected string $modelClass = Measure::class;
}
