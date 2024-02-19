<?php

namespace App\Actions\Leads\LeadGeneratorAssignment;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\LeadGeneratorAssignment;

class DeleteLeadGeneratorAssignmentAction extends AbstractDeleteAction
{
    protected string $modelClass = LeadGeneratorAssignment::class;
}
