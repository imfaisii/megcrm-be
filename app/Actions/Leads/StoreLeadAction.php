<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractCreateAction;
use App\Jobs\AircallContactCreationJob;
use App\Models\Lead;
use App\Models\LeadStatus;
use Illuminate\Support\Arr;

class StoreLeadAction extends AbstractCreateAction
{
    protected string $modelClass = Lead::class;

    public function create(array $data): Lead
    {
        $data = [
            ...$data,
            ...$data['address'],
        ];

        $fillables = Arr::except($data, [
            'has_second_receipent',
            'second_receipent',
            'measures',
        ]);

        /** @var Lead $lead */
        $lead = parent::create($fillables);
        AircallContactCreationJob::dispatch([$lead]);  // create contact on air call
        if ($data['has_second_receipent']) {
            $lead->secondReceipent()->firstOrCreate($data['second_receipent']);
        }

        $lead->setStatus(LeadStatus::first()->name, 'Created');

        // creating additional empty record for lead
        $lead->leadCustomerAdditionalDetail()->create();

        // adding benefits
        $lead->benefits()->syncWithPivotValues($data['benefits'], [
            'created_by_id' => auth()->id(),
        ]);

        return $lead;
    }
}
