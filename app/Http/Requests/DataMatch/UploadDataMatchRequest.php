<?php

namespace App\Http\Requests\DataMatch;

use Illuminate\Foundation\Http\FormRequest;

class UploadDataMatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return auth()->user()->can('upload_data_match');/
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
            'file' => ['required', 'file', 'mimes:csv','extensions:csv']
        ];
    }
}
