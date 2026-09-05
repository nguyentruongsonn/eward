<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SignResultFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signature_method' => ['nullable', 'string', 'in:internal_sha256'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
