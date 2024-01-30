<?php

namespace App\Actions\Leads\Statuses;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\LeadStatus;

class UpdateLeadStatusAction extends AbstractUpdateAction
{
    protected string $modelClass = LeadStatus::class;
}
