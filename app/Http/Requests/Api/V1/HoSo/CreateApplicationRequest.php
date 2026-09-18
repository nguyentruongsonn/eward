<?php

namespace App\Http\Requests\Api\V1\HoSo;

use Illuminate\Foundation\Http\FormRequest;

class CreateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'procedure_id' => ['required', 'integer', 'exists:tthc,maTTHC'],
            'data' => ['required', 'array', 'max:100'],
            'delivery_method' => ['required', 'in:online,direct'],
            'payment_method' => ['sometimes', 'string', 'in:online,direct'],
            'fee_items' => ['sometimes', 'array', 'max:50'],
            'fee_items.*.id' => ['required', 'integer'],
            'fee_items.*.quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ];
    }
}
