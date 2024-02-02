<?php

namespace App\Actions\FuelTypes;

use App\Actions\Common\AbstractListAction;
use App\Models\FuelType;

class ListFuelTypeAction extends AbstractListAction
{
    protected string $modelClass = FuelType::class;
}
