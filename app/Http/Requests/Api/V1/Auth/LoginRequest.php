<?php

namespace App\Http\Requests\Api\V1\Auth;

use App\Models\Nguoi;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $identifier = strtolower(trim((string) $this->input('identifier', $this->input('email'))));
        $email = $identifier;

        if (preg_match('/^\d{12}$/', $identifier) === 1) {
            $email = Nguoi::query()->where('maCCCD', $identifier)->value('email') ?? $identifier.'@invalid.local';
        }

        $this->merge([
            'identifier' => $identifier,
            'email' => $email,
        ]);
    }

    public function rules(): array
    {
        return [
            'identifier' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail): void {
                    if (! filter_var($value, FILTER_VALIDATE_EMAIL) && preg_match('/^\d{12}$/', $value) !== 1) {
                        $fail('Tài khoản phải là email hợp lệ hoặc CCCD gồm 12 chữ số.');
                    }
                },
            ],
            'email' => ['required', 'email:rfc', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'max:72'],
        ];
    }
}
