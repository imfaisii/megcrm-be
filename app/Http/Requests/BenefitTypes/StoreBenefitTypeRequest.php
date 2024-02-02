<?php

namespace App\Http\Requests\BenefitTypes;

use App\Actions\Common\BaseFormRequest;

class StoreBenefitTypeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
