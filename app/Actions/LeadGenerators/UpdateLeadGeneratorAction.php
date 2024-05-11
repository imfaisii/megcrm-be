<?php

namespace App\Actions\LeadGenerators;

use App\Actions\Common\AbstractUpdateAction;
use App\Models\LeadGenerator;
use Illuminate\Support\Arr;

class UpdateLeadGeneratorAction extends AbstractUpdateAction
{
    protected string $modelClass = LeadGenerator::class;

    public function update(mixed $model, array $data): mixed
    {
        $leadGenerator = parent::update($model, Arr::except($data, ['lead_generator_managers']));

        if (Arr::has($data, ['lead_generator_managers'])) {
            $leadGenerator->leadGeneratorManagers()->syncWithPivotValues($data['lead_generator_managers'], [
                'created_by_id' => auth()->id(),
            ]);
        }

        return $leadGenerator;
    }
}
