<?php

namespace App\Actions\Measures;

use App\Actions\Common\AbstractCreateAction;
use App\Models\Measure;

class StoreMeasureAction extends AbstractCreateAction
{
    protected string $modelClass = Measure::class;
}
