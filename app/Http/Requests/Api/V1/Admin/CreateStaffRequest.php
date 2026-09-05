<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateStaffRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'maCCCD' => ['required', 'string', 'max:20', Rule::unique('nguoi', 'maCCCD')],
            'hoTen' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255', Rule::unique('nguoi', 'email')],
            'password' => ['required', 'string', 'min:8', 'max:72'],
            'soDienThoai' => ['nullable', 'string', 'max:20'],
            'gioiTinh' => ['nullable', 'string', 'max:10'],
            'ngaySinh' => ['nullable', 'date'],
            'queQuan' => ['nullable', 'string', 'max:255'],
            'noiThuongTru' => ['nullable', 'string', 'max:255'],
            'noiTamTru' => ['nullable', 'string', 'max:255'],
            'vaiTro' => ['required', Rule::in([Role::OneStopOfficer->value, Role::CaseOfficer->value])],
            'maQuayLamViec' => ['nullable', 'integer', 'exists:quaylamviec,maQuayLamViec'],
        ];
    }
}
