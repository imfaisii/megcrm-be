<?php

namespace App\Actions\Leads\LeadGeneratorAssignment;

use App\Actions\Common\AbstractCreateAction;
use App\Models\LeadGeneratorAssignment;
use App\Models\User;

class StoreLeadGeneratorAssignmentAction extends AbstractCreateAction
{
    protected string $modelClass = LeadGeneratorAssignment::class;

    public function create(array $data): LeadGeneratorAssignment
    {
        $user = User::where('id', $data['user_id'])->first();

        $user->leadGeneratorAssignments()->syncWithPivotValues($data['lead_generator_assignments'], [
            'created_by_id' => auth()->id(),
        ]);

        return LeadGeneratorAssignment::make();
    }
}
