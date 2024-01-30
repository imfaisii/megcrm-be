<?php

namespace App\Actions\Leads\Statuses;

use App\Actions\Common\AbstractCreateAction;
use App\Models\LeadStatus;

class StoreLeadStatusAction extends AbstractCreateAction
{
    protected string $modelClass = LeadStatus::class;

    public function create(array $data): LeadStatus
    {
        $data['created_by_id'] = auth()->id() ?? 1;

        /** @var LeadStatus $leadStatus */
        return parent::create($data);
    }
}
