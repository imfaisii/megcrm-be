<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\BenefitType;
use App\Models\Lead;
use App\Models\LeadCustomerAdditionalDetail;
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
        $lead->leadCustomerAdditionalDetail->update($data['lead_customer_additional_detail']);

        $oldBenefits = $lead->benefits()->pluck('name');

        // adding benefits
        $lead->benefits()->syncWithPivotValues($data['benefits'], [
            'created_by_id' => auth()->id()
        ]);

        $newBenefits = BenefitType::whereIn('id', $data['benefits'])->pluck('name');

        if ($newBenefits != $oldBenefits) {
            $attributes = [];
            $old = [];

            if ($newBenefits != $oldBenefits) {
                $attributes['benefits'] = $newBenefits;
                $old['benefits'] = $oldBenefits;
            }

            activity()
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->withProperties([
                    'attributes' => $attributes,
                    'old' => $old
                ])
                ->event('updated')
                ->log('This record has been updated');
        }


        return $lead;
    }
}
