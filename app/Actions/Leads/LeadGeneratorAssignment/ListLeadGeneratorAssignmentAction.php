<?php

namespace App\Actions\Leads\LeadGeneratorAssignment;

use App\Actions\Common\AbstractListAction;
use App\Models\LeadGeneratorAssignment;

class ListLeadGeneratorAssignmentAction extends AbstractListAction
{
    protected string $modelClass = LeadGeneratorAssignment::class;
}
