<?php

namespace App\Http\Requests\Companies;

use App\Actions\Common\BaseFormRequest;

class UpdateCompanyRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:companies,name,' . $this->route('company')->id],
            'address' => ['required', 'string', 'unique:companies,address,' . $this->route('company')->id],
            'company_number' => ['nullable', 'string'],
            'vat_number' => ['nullable', 'string'],
            'account_number' => ['nullable', 'string'],
            'sort_code' => ['nullable', 'string'],
            'policy_reference' =>  ['nullable', 'string'],
            'public_liability_number' =>  ['nullable', 'string'],
        ];
    }
}
