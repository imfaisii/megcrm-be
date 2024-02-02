<?php

namespace App\Http\Requests\Surveyors;

use App\Actions\Common\BaseFormRequest;

class StoreSurveyorRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
