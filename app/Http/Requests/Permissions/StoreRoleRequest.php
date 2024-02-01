<?php

namespace App\Http\Requests\Permissions;

use App\Actions\Common\BaseFormRequest;

class StoreRoleRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:roles,name'],
            'permissions' => ['array']
        ];
    }
}
