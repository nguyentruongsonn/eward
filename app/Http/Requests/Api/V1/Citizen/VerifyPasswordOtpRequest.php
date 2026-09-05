<?php

namespace App\Http\Requests\Api\V1\Citizen;

use Illuminate\Foundation\Http\FormRequest;

class VerifyPasswordOtpRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'challenge_id' => ['required', 'uuid'],
            'code' => ['required', 'digits:6'],
            'new_password' => ['required', 'confirmed', 'string', 'min:8', 'max:72'],
        ];
    }
}
