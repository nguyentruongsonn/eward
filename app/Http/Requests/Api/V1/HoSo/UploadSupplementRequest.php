<?php

namespace App\Http\Requests\Api\V1\HoSo;

use Illuminate\Foundation\Http\FormRequest;

class UploadSupplementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'documents' => ['required', 'array', 'min:1', 'max:10'],
            'documents.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'document_types' => ['required', 'array', 'size:'.count((array) $this->file('documents'))],
            'document_types.*' => ['required', 'integer', 'exists:giayto,maGiayTo'],
        ];
    }
}
