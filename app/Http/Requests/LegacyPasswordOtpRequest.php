<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegacyPasswordOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'digits:6'],
            'new_password' => ['required', 'confirmed', 'string', 'min:6'],
        ];
    }
}
