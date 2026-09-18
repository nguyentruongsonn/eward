<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateSupplementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'document_ids' => ['required', 'array', 'min:1', 'max:20'],
            'document_ids.*' => ['required', 'integer', 'distinct', 'exists:giayto,maGiayTo'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
