<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LegacySupplementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'maGiayTo' => ['required', 'array', 'size:'.count((array) $this->file('files'))],
            'maGiayTo.*' => ['required', 'integer', 'exists:giayto,maGiayTo'],
        ];
    }
}
