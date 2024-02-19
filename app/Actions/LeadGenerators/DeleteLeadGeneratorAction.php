<?php

namespace App\Actions\LeadGenerators;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\LeadGenerator;

class DeleteLeadGeneratorAction extends AbstractDeleteAction
{
    protected string $modelClass = LeadGenerator::class;
}
