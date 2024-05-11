<?php

namespace App\Http\Requests\LeadGenerators;

use App\Actions\Common\BaseFormRequest;

class StoreLeadGeneratorRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'sender_id' => ['required', 'string', 'max:11'],
            'email' => ['required', 'email'],
            'phone_no' => ['required', 'numeric', 'digits:10,10'],
            'aircall_number' => ['nullable', 'numeric', 'digits:10,10'],
            'lead_generator_managers' => ['required', 'array'],
        ];
    }
}
