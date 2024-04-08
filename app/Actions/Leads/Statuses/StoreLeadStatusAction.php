<?php

namespace App\Actions\Leads\Statuses;

use App\Actions\Common\AbstractCreateAction;
use App\Models\LeadStatus;

class StoreLeadStatusAction extends AbstractCreateAction
{
    protected string $modelClass = LeadStatus::class;

    public function create(array $data): LeadStatus
    {
        if ($data['color'] === 'warning') {
            $data['color'] = '#E4A11B';
        }

        return parent::create($data);
    }
}
