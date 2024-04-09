<?php

namespace App\Http\Requests\File;

use App\Enums\AppEnum;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            'image' => ['required', 'max:8192', 'file'],
            'collection_name' => ["required", "string", "in:" . implode(",", AppEnum::CustomerLeadCollectionsList()) . ""],
        ];
    }
}
