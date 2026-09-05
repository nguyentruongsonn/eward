<?php

namespace App\Http\Requests\Api\V1\Citizen;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('email')) {
            $data['email'] = strtolower(trim((string) $this->input('email')));
        }

        if ($this->has('phone')) {
            $data['phone'] = preg_replace('/\D+/', '', (string) $this->input('phone'));
        }

        $this->merge($data);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => [
                'sometimes',
                'required',
                'email:rfc',
                'max:255',
                Rule::unique('nguoi', 'email')->ignore($this->user('api')?->getKey(), 'IDnguoiDung'),
            ],
            'phone' => ['sometimes', 'required', 'digits_between:9,10'],
        ];
    }
}
