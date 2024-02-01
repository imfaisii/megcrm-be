<?php

namespace App\Http\Requests\Users;

use App\Actions\Common\BaseFormRequest;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class StoreUserRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Password::defaults()],
            'roles' => ['array'],
            'is_active' => ['boolean']
        ];
    }
}
