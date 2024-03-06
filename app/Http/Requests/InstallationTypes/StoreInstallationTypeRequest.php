<?php

namespace App\Http\Requests\InstallationTypes;

use App\Actions\Common\BaseFormRequest;

class StoreInstallationTypeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'measures' => ['nullable', 'array'],
        ];
    }
}
