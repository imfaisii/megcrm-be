<?php

namespace App\Actions\Leads\Statuses;

use App\Actions\Common\AbstractListAction;
use App\Models\LeadStatus;

class ListLeadStatusAction extends AbstractListAction
{
    protected string $modelClass = LeadStatus::class;
}
