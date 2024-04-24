<?php

namespace App\Actions\Leads;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\BenefitType;
use App\Models\Lead;
use App\Models\Measure;
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

        $lead = parent::update($lead, Arr::except($data, ['lead_customer_additional_detail', 'lead_additional']));

        $this->updateLeadRelations($lead, $data);

        return $lead;
    }

    public function updateLeadRelations(Lead $lead, array $data)
    {
        // updating relation
        if (isset($data['lead_customer_additional_detail'])) {
            $lead->leadCustomerAdditionalDetail->update($data['lead_customer_additional_detail']);
        }

        if (isset($data['lead_additional'])) {
            $lead->leadAdditional()->updateOrCreate([
                'lead_id' => $lead->id,
            ], $data['lead_additional']);
        }

        if (isset($data['survey_booking'])) {
            $lead->surveyBooking()->updateOrCreate([
                'lead_id' => $lead->id,
            ], $data['survey_booking']);
        }

        if (isset($data['installation_bookings'])) {
            foreach ($data['installation_bookings'] as $key => $installation) {
                $lead->installationBookings()->updateOrCreate([
                    'lead_id' => $lead->id,
                    'measure_id' => $installation['measure_id'],
                ], $installation);
            }
        }

        if (Arr::has($data, 'benefits')) {
            $this->updateLeadBenefits($lead, $data);
        }

        if (Arr::has($data, 'measures')) {
            $this->updateLeadMeasures($lead, $data);
        }

        if (Arr::has($data, 'submission')) {
            $lead->submission()->updateOrCreate(['lead_id' => $lead->id], $data['submission']);
        }

        if ($data['has_second_receipent'] && Arr::has($data, 'second_receipent')) {
            $lead->secondReceipent()->updateOrCreate(['lead_id' => $lead->id], $data['second_receipent']);
        }
    }

    public function updateLeadBenefits(Lead $lead, array $data)
    {
        $oldBenefits = $lead->benefits()->pluck('name');

        // adding benefits
        $lead->benefits()->syncWithPivotValues($data['benefits'], [
            'created_by_id' => auth()->id(),
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
                    'old' => $old,
                ])
                ->event('updated')
                ->log('This record has been updated');
        }
    }

    public function updateLeadMeasures(Lead $lead, array $data)
    {
        $oldMeasures = $lead->measures()->pluck('name');

        // adding measures
        $lead->measures()->syncWithPivotValues($data['measures'], [
            'created_by_id' => auth()->id(),
        ]);

        $newMeasures = Measure::whereIn('id', $data['measures'])->pluck('name');

        if ($newMeasures != $oldMeasures) {
            $attributes = [];
            $old = [];

            if ($newMeasures != $oldMeasures) {
                $attributes['benefits'] = $newMeasures;
                $old['benefits'] = $oldMeasures;
            }

            activity()
                ->causedBy(auth()->user())
                ->performedOn($lead)
                ->withProperties([
                    'attributes' => $attributes,
                    'old' => $old,
                ])
                ->event('updated')
                ->log('This record has been updated');
        }
    }
}
