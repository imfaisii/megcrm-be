<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractCreateAction;
use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Support\Arr;

class StoreLeadAction extends AbstractCreateAction
{
    protected string $modelClass = Lead::class;

    public function create(array $data): Lead
    {
        $data['created_by_id'] = auth()->id() ?? 1;

        /** @var Lead $lead */
        $lead = parent::create(Arr::except($data, [
            'has_second_receipent',
            'second_receipent',
            'measures'
        ]));

        if ($data['has_second_receipent']) {
            $lead->secondReceipent()->firstOrCreate($data['second_receipent']);
        }

        $lead->setStatus(LeadStatus::first()->name, 'Created');

        return $lead;
    }
}
