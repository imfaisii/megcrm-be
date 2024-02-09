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
            'email' => ['required', 'email'],
            'phone_no' => ['required', 'string'],
            'dob' => ['required', 'string'],
            'address' => ['required', 'string', 'unique:leads,address'],
            'post_code' => ['required', 'string'],
            'measures' => ['array'],
            'has_second_receipent' => ['required', 'boolean'],
            'second_receipent' => ['required', 'array'],
            'is_marked_as_job' => ['required', 'boolean'],
            'job_type_id' => ['nullable', 'exists:job_types,id'],
            'fuel_type_id' => ['nullable', 'exists:fuel_types,id'],
            'surveyor_id' => ['nullable', 'exists:surveyors,id'],
            'lead_generator_id' => ['nullable', 'exists:lead_generators,id'],
            'lead_source_id' => ['nullable', 'exists:lead_sources,id'],
            'benefits' => ['nullable', 'array'],
            'comments' => ['nullable']
        ];
    }
}
