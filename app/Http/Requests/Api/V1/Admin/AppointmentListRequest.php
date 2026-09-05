<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\AppointmentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AppointmentListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'search' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'from_date' => ['nullable', 'date_format:Y-m-d'],
            'to_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:from_date'],
            'status' => ['nullable', Rule::in(array_map(static fn (AppointmentStatus $status): string => $status->value, AppointmentStatus::cases()))],
            'procedure_id' => ['nullable', 'integer', 'exists:tthc,maTTHC'],
        ];
    }
}
