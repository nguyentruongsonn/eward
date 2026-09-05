<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ForwardApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'leader_id' => ['nullable', 'integer', 'exists:nguoi,IDnguoiDung'],
            'note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
