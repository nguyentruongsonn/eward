<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateProcedureRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:500'],
            'field_id' => ['required', 'integer', 'exists:linhvuc,maLinhVuc'],
            'counter_id' => ['nullable', 'integer', 'exists:quaylamviec,maQuayLamViec'],
            'instructions' => ['required', 'string'],
            'target' => ['required', 'string', 'max:255'],
            'agency' => ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['Công khai', 'Chờ công khai', 'Bãi bỏ'])],
            'requirements' => ['required', 'string'],
            'legal_basis' => ['required', 'string'],
            'result' => ['required', 'string', 'max:500'],
            'audience_ids' => ['nullable', 'array'],
            'audience_ids.*' => ['integer', 'exists:doituongthuchien,maDoiTuong'],
            'methods' => ['nullable', 'array'],
            'methods.*.channel' => ['required', 'string', 'max:255'],
            'methods.*.resolution_time' => ['nullable', 'string'],
            'methods.*.fee_description' => ['nullable', 'string'],
            'methods.*.duration' => ['nullable', 'integer', 'min:0'],
            'methods.*.description' => ['nullable', 'string'],
            'fees' => ['nullable', 'array'],
            'fees.*.type' => ['required', 'string', 'max:255'],
            'fees.*.amount' => ['nullable', 'numeric', 'min:0'],
            'fees.*.required' => ['nullable', Rule::in(['Có', 'Không'])],
            'fees.*.description' => ['nullable', 'string', 'max:2000'],
            'form_config' => ['nullable', 'json'],
            'components' => ['nullable', 'array'],
            'components.*.name' => ['required', 'string', 'max:500'],
            'components.*.documents' => ['nullable', 'array'],
            'components.*.documents.*.document_id' => ['required', 'integer', 'exists:giayto,maGiayTo'],
            'components.*.documents.*.original_copies' => ['nullable', 'integer', 'min:0'],
            'components.*.documents.*.duplicate_copies' => ['nullable', 'integer', 'min:0'],
        ];
    }
}
