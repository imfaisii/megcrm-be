<?php

namespace App\Http\Requests\Users;

use App\Actions\Common\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string'],
            'email' => [
                'nullable',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'current_password' => ['required_with:password,', 'current_password', 'string'],
            'password' => ['required_with:current_password', 'confirmed', 'string'],
        ];
    }
}
