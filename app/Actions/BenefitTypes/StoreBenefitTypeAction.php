<?php

namespace App\Actions\BenefitTypes;

use App\Actions\Common\AbstractCreateAction;
use App\Models\BenefitType;

class StoreBenefitTypeAction extends AbstractCreateAction
{
    protected string $modelClass = BenefitType::class;
}
