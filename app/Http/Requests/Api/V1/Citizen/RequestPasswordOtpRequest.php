<?php

namespace App\Http\Requests\Api\V1\Citizen;

use Illuminate\Foundation\Http\FormRequest;

class RequestPasswordOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'min:8', 'max:72'],
        ];
    }
}
