<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class ApplicationListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Role::normalize($this->user('api')?->vaiTro)?->isStaff() ?? false;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', 'integer', 'exists:trangthaihoso,maTrangThai'],
            'procedure_id' => ['nullable', 'integer', 'exists:tthc,maTTHC'],
            'citizen' => ['nullable', 'string', 'max:255'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from'],
            'sort' => ['nullable', 'in:received_at,id,status'],
            'direction' => ['nullable', 'in:asc,desc'],
            'overdue' => ['nullable'],
        ];
    }
}
