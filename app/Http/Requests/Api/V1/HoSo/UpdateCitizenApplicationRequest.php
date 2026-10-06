<?php

namespace App\Http\Requests\Api\V1\HoSo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCitizenApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'data' => ['required', 'array', 'max:100'],
        ];
    }
}
