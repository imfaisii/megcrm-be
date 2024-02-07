<?php

namespace App\Http\Requests\Leads\LeadGeneratorAssignment;

use App\Actions\Common\BaseFormRequest;

class StoreLeadGeneratorAssignmentRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'user_id' => ['required', 'exists:users,id'],
            'lead_generator_assignments' => ['required', 'array']
        ];
    }
}
