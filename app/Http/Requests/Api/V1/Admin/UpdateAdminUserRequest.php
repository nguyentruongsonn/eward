<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'full_name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email:rfc', 'max:255', Rule::unique('nguoi', 'email')->ignore($userId, 'IDnguoiDung')],
            'password' => ['sometimes', 'string', 'min:12', 'max:72'],
            'phone' => ['sometimes', 'digits_between:9,10'],
            'role' => ['sometimes', Rule::in(array_map(fn (Role $role): string => $role->value, Role::cases()))],
        ];
    }
}
