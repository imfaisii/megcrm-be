<?php

namespace App\Http\Requests\Users;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($this->route('user')),
            ],
            'password' => ['nullable', 'confirmed', \Illuminate\Validation\Rules\Password::defaults()],
            'roles' => ['array'],
            'is_active' => ['boolean'],
            'aircall_email_address' => ['nullable', 'email'],
            'additional' => ['nullable', 'array'],
            'additional.dob' => ['nullable', 'string'],
            'additional.gender' => ['nullable', 'string'],
            'additional.address' => ['nullable', 'string'],
            'additional.phone_no' => ['nullable', 'numeric'],
            'installation_types' => ['nullable', 'array']
        ];
    }
}
