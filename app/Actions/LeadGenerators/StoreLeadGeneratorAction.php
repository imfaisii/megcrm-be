<?php

namespace App\Actions\LeadGenerators;

use App\Actions\Common\AbstractCreateAction;
use App\Models\LeadGenerator;

class StoreLeadGeneratorAction extends AbstractCreateAction
{
    protected string $modelClass = LeadGenerator::class;
}
