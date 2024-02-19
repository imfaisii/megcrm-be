<?php

namespace App\Actions\BenefitTypes;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\BenefitType;

class DeleteBenefitTypeAction extends AbstractDeleteAction
{
    protected string $modelClass = BenefitType::class;
}
