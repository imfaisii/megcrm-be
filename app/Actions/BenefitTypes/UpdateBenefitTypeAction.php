<?php

namespace App\Actions\BenefitTypes;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\BenefitType;

class UpdateBenefitTypeAction extends AbstractUpdateAction
{
    protected string $modelClass = BenefitType::class;
}
