<?php

namespace App\Http\Requests\DataMatch;

use Illuminate\Foundation\Http\FormRequest;

class GetFilesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return auth()->user()->can('view_data_match_files');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'url' => ['required', 'string'],
            'uuid' => ['required', 'string', 'exists:data_match_files,id'],
        ];
    }
}
