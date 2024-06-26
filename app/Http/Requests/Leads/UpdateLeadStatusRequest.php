<?php

namespace App\Http\Requests\Leads;

use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadStatusRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'exists:lead_statuses,name'],
            'comments' => ['required', 'max:255']
        ];
    }
}
