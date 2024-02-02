<?php

namespace App\Actions\Measures;

use App\Actions\Common\AbstractListAction;
use App\Models\Measure;

class ListMeasureAction extends AbstractListAction
{
    protected string $modelClass = Measure::class;
}
