<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('nguoi', 'email')],
            'password' => ['required', 'string', 'min:12', 'max:72'],
            'phone' => ['required', 'digits_between:9,10'],
            'citizen_id' => ['nullable', 'string', 'max:50'],
            'role' => ['required', Rule::in(array_map(fn (Role $role): string => $role->value, Role::cases()))],
        ];
    }
}
