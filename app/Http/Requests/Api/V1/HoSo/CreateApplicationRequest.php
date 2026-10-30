<?php

namespace App\Http\Requests\Api\V1\HoSo;

use Illuminate\Foundation\Http\FormRequest;

class CreateApplicationRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        foreach (['data', 'fee_items'] as $field) {
            $value = $this->input($field);
            if (! is_string($value)) {
                continue;
            }

            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $this->merge([$field => $decoded]);
            }
        }
    }

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
            'documents' => ['sometimes', 'array', 'max:50'],
            'documents.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'document_types' => ['sometimes', 'array', 'max:50'],
            'document_types.*' => ['required', 'integer', 'min:1', 'exists:giayto,maGiayTo'],
        ];
    }
}
