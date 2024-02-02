<?php

namespace App\Actions\Leads\Statuses;

use App\Actions\Common\AbstractCreateAction;
use App\Models\LeadStatus;

class StoreLeadStatusAction extends AbstractCreateAction
{
    protected string $modelClass = LeadStatus::class;
}
