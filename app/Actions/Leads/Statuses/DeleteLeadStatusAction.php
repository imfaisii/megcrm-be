<?php

namespace App\Actions\Leads\Statuses;

use App\Actions\Common\AbstractDeleteAction;
use App\Models\LeadStatus;

class DeleteLeadStatusAction extends AbstractDeleteAction
{
    protected string $modelClass = LeadStatus::class;
}
