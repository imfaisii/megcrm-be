<?php

namespace App\Http\Requests\Leads;

use App\Actions\Common\BaseFormRequest;

class StoreLeadRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['required', 'string'],
            'email' => ['nullable', 'string'],
            'phone_no' => ['required', 'string'],
            'dob' => ['required', 'string'],
            'address' => ['required', 'array'],
            'address.address' => ['required', 'string', 'unique:leads,address'],
            'address.*' => ['nullable',],
            'post_code' => ['required', 'string'],
            'measures' => ['array'],
            'has_second_receipent' => ['sometimes', 'required', 'boolean'],
            'second_receipent' => ['sometimes', 'required', 'array'],
            'second_receipent.first_name' => [
                'sometimes',
                'nullable',
                'required_if:has_second_receipent,true',
                'string',
            ],
            'second_receipent.last_name' => [
                'sometimes',
                'nullable',
                'required_if:has_second_receipent,true',
                'string',
            ],
            'second_receipent.middle_name' => [
                'sometimes',
                'nullable',
                'nullable',
                'string',
            ],
            'second_receipent.dob' => [
                'sometimes',
                'nullable',
                'required_if:has_second_receipent,true',
                'date',
            ],
            'is_marked_as_job' => ['required', 'boolean'],
            'job_type_id' => ['nullable', 'exists:job_types,id'],
            'fuel_type_id' => ['nullable', 'exists:fuel_types,id'],
            'surveyor_id' => ['nullable', 'exists:users,id'],
            'lead_generator_id' => ['nullable', 'exists:lead_generators,id'],
            'lead_source_id' => ['nullable', 'exists:lead_sources,id'],
            'benefits' => ['nullable', 'array'],
            'notes' => ['nullable'],
        ];
    }

    public function attributes()
    {
        return [
            'has_second_receipent' => 'second receipent',
            'second_receipent.first_name' => 'second receipent first name',
            'second_receipent.middle_name' => 'second receipent middle name',
            'second_receipent.last_name' => 'second receipent last name',
            'second_receipent.dob' => 'second receipent date of birth',
        ];
    }
}
