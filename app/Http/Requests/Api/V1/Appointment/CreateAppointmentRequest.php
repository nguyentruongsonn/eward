<?php

namespace App\Http\Requests\Api\V1\Appointment;

use Illuminate\Foundation\Http\FormRequest;

class CreateAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'procedure_id' => ['required', 'integer', 'exists:tthc,maTTHC'],
            'scheduled_at' => ['required', 'date_format:Y-m-d H:i', 'after:now'],
        ];
    }
}
