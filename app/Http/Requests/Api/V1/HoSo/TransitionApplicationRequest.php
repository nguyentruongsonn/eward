<?php

namespace App\Http\Requests\Api\V1\HoSo;

use Illuminate\Foundation\Http\FormRequest;

class TransitionApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'integer', 'between:1,11'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
