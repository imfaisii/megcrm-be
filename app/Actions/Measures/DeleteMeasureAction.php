<?php

namespace App\Actions\Measures;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\Measure;

class DeleteMeasureAction extends AbstractDeleteAction
{
    protected string $modelClass = Measure::class;
}
