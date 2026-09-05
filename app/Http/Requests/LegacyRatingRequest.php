<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegacyRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'soDiem' => ['required', 'integer', 'min:1', 'max:5'],
            'nhanXet' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
