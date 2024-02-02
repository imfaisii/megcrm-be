<?php

namespace App\Actions\LeadGenerators;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\LeadGenerator;

class UpdateLeadGeneratorAction extends AbstractUpdateAction
{
    protected string $modelClass = LeadGenerator::class;
}
