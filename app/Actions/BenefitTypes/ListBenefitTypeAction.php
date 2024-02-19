<?php

namespace App\Actions\BenefitTypes;

use App\Actions\Common\AbstractListAction;
use App\Models\BenefitType;

class ListBenefitTypeAction extends AbstractListAction
{
    protected string $modelClass = BenefitType::class;
}
