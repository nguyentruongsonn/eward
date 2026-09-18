<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SendApplicationMailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:lien_lac,bo_sung'],
            'subject' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:50000'],
        ];
    }
}
