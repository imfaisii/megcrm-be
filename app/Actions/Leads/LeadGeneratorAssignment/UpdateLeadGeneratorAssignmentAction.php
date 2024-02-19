<?php

namespace App\Actions\Leads\LeadGeneratorAssignment;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\LeadGeneratorAssignment;

class UpdateLeadGeneratorAssignmentAction extends AbstractUpdateAction
{
    protected string $modelClass = LeadGeneratorAssignment::class;
}
