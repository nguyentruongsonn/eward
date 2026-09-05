<?php

namespace App\Http\Requests\Api\V1\HoSo;

use Illuminate\Foundation\Http\FormRequest;

class ResultFileListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }
}
