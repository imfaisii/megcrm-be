<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\Lead;
use Illuminate\Support\Arr;

class UpdateLeadAction extends AbstractUpdateAction
{
    protected string $modelClass = Lead::class;

    public function update(mixed $lead, array $data): mixed
    {
        /** @var Lead $lead */
        $data = array_filter($data, function ($value) {
            // Remove null values and empty strings
            return $value !== null && $value !== '';
        });

        $lead = parent::update($lead, Arr::except($data, ['lead_customer_additional_detail']));


        // updating relation
        $lead->leadCustomerAdditionalDetail()->update($data['lead_customer_additional_detail']);

        return $lead;
    }
}
