<?php

namespace App\Http\Requests\team;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use Str;

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
        $rules = [
            'name' => ['required', 'string', 'unique:teams,name,NULL,id,admin_id,' . $this->admin_id,],   // like for each admin id it should be unique
            'admin_id' => ['required', 'integer', 'exists:users,id'],
            'members' => ['required', 'array'],
            'members.*' => ['required', 'exists:users,id'],
        ];
        if (Str::contains($this->route()->getName(), 'update')) {
            data_set($rules, 'name.2', "unique:teams,name,{$this->route('team')?->id},id,admin_id,{$this->admin_id}");   // update rule for the updating the team name
        }
        return $rules;
    }
}
