<?php

namespace App\Http\Requests\Companies;

use App\Actions\Common\BaseFormRequest;

class StoreCompanyRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:companies'],
            'address' => ['required', 'string', 'unique:companies'],
            'company_number' => ['nullable', 'string'],
            'vat_number' => ['nullable', 'string'],
            'account_number' => ['nullable', 'string'],
            'sort_code' => ['nullable', 'string'],
            'policy_reference' =>  ['nullable', 'string'],
            'public_liability_number' =>  ['nullable', 'string'],
        ];
    }
}
