<?php

namespace App\Http\Requests\Api\V1\Admin;

use App\Enums\Role;
use Illuminate\Foundation\Http\FormRequest;

class ConfirmCounterPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array(Role::normalize($this->user('api')?->vaiTro), [Role::OneStopOfficer, Role::Administrator], true);
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:0'],
            'receipt_number' => ['required', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
