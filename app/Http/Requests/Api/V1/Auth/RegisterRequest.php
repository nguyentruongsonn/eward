<?php

namespace App\Http\Requests\Api\V1\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'email' => strtolower(trim((string) $this->input('email'))),
            'phone' => preg_replace('/\D+/', '', (string) $this->input('phone')),
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'min:2', 'max:100', 'regex:/^[\p{L}\s\'-]+$/u'],
            'citizen_id' => ['required', 'string', 'regex:/^\d{12}$/'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('nguoi', 'email')],
            'password' => ['required', 'string', 'min:8', 'max:72', 'confirmed'],
            'phone' => ['required', 'digits_between:9,10'],
            'gender' => ['nullable', 'string', 'in:Nam,Nữ'],
            'birth_date' => ['nullable', 'date', 'before:today', 'after:1900-01-01'],
            'hometown' => ['nullable', 'string', 'max:255'],
            'permanent_address' => ['nullable', 'string', 'max:500'],
            'temporary_address' => ['nullable', 'string', 'max:500'],
        ];
    }
}
