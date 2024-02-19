<?php

namespace App\Actions\FuelTypes;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\FuelType;

class UpdateFuelTypeAction extends AbstractUpdateAction
{
    protected string $modelClass = FuelType::class;
}
