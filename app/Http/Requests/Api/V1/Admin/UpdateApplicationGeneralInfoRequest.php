<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationGeneralInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hoTen' => ['nullable', 'string', 'max:255'],
            'ngaySinh' => ['nullable', 'date'],
            'gioiTinh' => ['nullable', 'string', 'max:20'],
            'cccd' => ['nullable', 'string', 'max:20'],
            'ngayCap' => ['nullable', 'date'],
            'noiCap' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'soDienThoai' => ['nullable', 'string', 'max:20'],
            'diaChi' => ['nullable', 'string', 'max:500'],
        ];
    }
}
