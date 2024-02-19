<?php

namespace App\Actions\LeadGenerators;

use App\Actions\Common\AbstractListAction;
use App\Models\LeadGenerator;

class ListLeadGeneratorAction extends AbstractListAction
{
    protected string $modelClass = LeadGenerator::class;
}
