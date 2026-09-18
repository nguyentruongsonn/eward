<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProcedureRequest extends FormRequest
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
            'audience_ids' => ['sometimes', 'array', 'max:50'],
            'audience_ids.*' => ['integer', 'exists:doituongthuchien,maDoiTuong'],
            'methods' => ['sometimes', 'array', 'max:20'],
            'methods.*.channel' => ['required_with:methods', 'string', 'max:255'],
            'methods.*.resolution_time' => ['nullable', 'string', 'max:255'],
            'methods.*.fee_description' => ['nullable', 'string', 'max:500'],
            'methods.*.duration' => ['nullable', 'integer', 'min:0', 'max:3650'],
            'methods.*.description' => ['nullable', 'string', 'max:1000'],
            'fees' => ['sometimes', 'array', 'max:50'],
            'fees.*.type' => ['required_with:fees', 'string', 'max:255'],
            'fees.*.amount' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'fees.*.required' => ['nullable', 'string', 'max:100'],
            'fees.*.description' => ['nullable', 'string', 'max:500'],
            'form_config' => ['sometimes', 'nullable', 'json'],
            'components' => ['sometimes', 'array', 'max:50'],
            'components.*.name' => ['required_with:components', 'string', 'max:500'],
            'components.*.documents' => ['sometimes', 'array', 'max:50'],
            'components.*.documents.*.document_id' => ['required', 'integer', 'exists:giayto,maGiayTo'],
            'components.*.documents.*.original_copies' => ['nullable', 'integer', 'min:0', 'max:1000'],
            'components.*.documents.*.duplicate_copies' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ];
    }
}
