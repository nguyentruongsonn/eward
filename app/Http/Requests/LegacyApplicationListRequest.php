<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegacyApplicationListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ten_dich_vu' => ['nullable', 'string', 'max:255'],
            'ma_ho_so' => ['nullable', 'string', 'max:100'],
            'trang_thai' => ['nullable', 'string', 'max:50'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
