<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\AppointmentStatus;
use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppointmentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = Role::normalize($this->user('api')?->vaiTro);

        return in_array($role, [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::enum(AppointmentStatus::class)],
        ];
    }
}
