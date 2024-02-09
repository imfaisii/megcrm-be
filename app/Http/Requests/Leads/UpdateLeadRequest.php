<?php

namespace App\Http\Requests\Leads;

use App\Actions\Common\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'first_name' => ['sometimes', 'required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'last_name' => ['sometimes', 'required', 'string'],
            'email' => ['sometimes', 'required', 'email'],
            'phone_no' => ['sometimes', 'required', 'string'],
            'dob' => ['sometimes', 'required', 'string'],
            'address' => ['sometimes', 'required', 'string'],
            'post_code' => ['sometimes', 'required', 'string'],
            'measures' => ['sometimes', 'array'],
            'has_second_receipent' => ['sometimes', 'required', 'boolean'],
            'second_receipent' => ['sometimes', 'required', 'array'],
            'is_marked_as_job' => ['sometimes', 'required', 'boolean'],
            'job_type_id' => ['sometimes', 'nullable', 'exists:job_types,id'],
            'fuel_type_id' => ['sometimes', 'nullable', 'exists:fuel_types,id'],
            'surveyor_id' => ['sometimes', 'nullable', 'exists:surveyors,id'],
            'lead_generator_id' => ['sometimes', 'nullable', 'exists:lead_generators,id'],
            'lead_source_id' => ['sometimes', 'nullable', 'exists:lead_sources,id'],
            'benefits' => ['sometimes', 'array'],
            'comments' => ['sometimes', 'nullable'],
            'lead_customer_additional_detail' => ['required', 'array'],
            'lead_customer_additional_detail.contact_method' => ['nullable', 'string'],
            'lead_customer_additional_detail.priority_type' => ['nullable', 'string'],
            'lead_customer_additional_detail.time_to_contact' => ['nullable', 'string'],
            'lead_customer_additional_detail.time_at_address' => ['nullable', 'date'],
            'lead_customer_additional_detail.is_customer_owner' => ['boolean', 'required'],
            'lead_customer_additional_detail.is_lead_shared' => ['boolean', 'required'],
            'lead_customer_additional_detail.is_datamatch_required' => ['boolean', 'required'],
            'lead_customer_additional_detail.datamatch_progress' => ['nullable', 'string'],
            'lead_customer_additional_detail.datamatch_progress_date' => ['nullable', 'date'],
            'lead_customer_additional_detail.lead_id' => ['required', 'exists:leads,id'],
        ];
    }
}
