<?php

namespace App\Http\Requests\FuelTypes;

use App\Actions\Common\BaseFormRequest;

class StoreFuelTypeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
