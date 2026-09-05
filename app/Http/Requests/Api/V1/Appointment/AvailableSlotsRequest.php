<?php

namespace App\Http\Requests\Api\V1\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class AvailableSlotsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'procedure_id' => ['required', 'integer', 'exists:tthc,maTTHC'],
            'date' => ['required', 'date_format:Y-m-d'],
        ];
    }
}
