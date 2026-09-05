<?php

namespace App\Http\Requests\Api\V1\Citizen;

use Illuminate\Foundation\Http\FormRequest;

class UpdateIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'citizen_id' => ['sometimes', 'nullable', 'string', 'max:50'],
            'gender' => ['sometimes', 'nullable', 'in:Nam,Nữ'],
            'birth_date' => ['sometimes', 'nullable', 'date', 'before:today'],
            'hometown' => ['sometimes', 'nullable', 'string', 'max:500'],
            'permanent_address' => ['sometimes', 'nullable', 'string', 'max:500'],
            'temporary_address' => ['sometimes', 'nullable', 'string', 'max:500'],
        ];
    }
}
