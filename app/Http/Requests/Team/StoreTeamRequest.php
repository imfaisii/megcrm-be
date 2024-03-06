<?php

namespace App\Http\Requests\team;

use Illuminate\Foundation\Http\FormRequest;

class StoreTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'unique:teams,name,NULL,id,admin_id,' . $this->admin_id,],   // like for each admin id it should be unique
            'admin_id' => ['required', 'integer', 'exists:users,id'],
            'members' => ['required', 'array'],
            'members.*' => ['required', 'exists:users,id'],
        ];
    }
}
