<?php

namespace App\Actions\FuelTypes;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\FuelType;

class DeleteFuelTypeAction extends AbstractDeleteAction
{
    protected string $modelClass = FuelType::class;
}
