<?php

namespace App\Actions\FuelTypes;

use App\Actions\Common\AbstractCreateAction;
use App\Models\FuelType;

class StoreFuelTypeAction extends AbstractCreateAction
{
    protected string $modelClass = FuelType::class;
}
