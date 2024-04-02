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
            'company_id' => ['nullable', 'numeric'],
            'is_associated_with_company' => ['nullable', 'boolean'],
            'additional' => ['nullable', 'array'],
            'additional.dob' => ['nullable', 'string'],
            'additional.gender' => ['nullable', 'string'],
            'additional.address' => ['nullable', 'string'],
            'additional.phone_no' => ['nullable', 'numeric'],
            'additional.nin' => ['nullable', 'string'],
            'additional.account_number' => ['nullable', 'string'],
            'additional.visa_expiry' => ['nullable', 'date'],
            'additional.bank' => ['nullable', 'string'],
            'installation_types' => ['nullable', 'array'],
        ];
    }
}
