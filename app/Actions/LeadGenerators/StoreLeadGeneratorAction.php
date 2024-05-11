<?php

namespace App\Actions\LeadGenerators;

use App\Actions\Common\AbstractCreateAction;
use App\Actions\Common\BaseModel;
use App\Models\LeadGenerator;
use Illuminate\Support\Arr;

class StoreLeadGeneratorAction extends AbstractCreateAction
{
    protected string $modelClass = LeadGenerator::class;

    public function create(array $data): BaseModel
    {
        $leadGenerator = parent::create(Arr::except($data, ['lead_generator_managers']));

        if (Arr::has($data, ['lead_generator_managers'])) {
            $leadGenerator->leadGeneratorManagers()->syncWithPivotValues($data['lead_generator_managers'], [
                'created_by_id' => auth()->id(),
            ]);
        }

        return $leadGenerator;
    }
}
