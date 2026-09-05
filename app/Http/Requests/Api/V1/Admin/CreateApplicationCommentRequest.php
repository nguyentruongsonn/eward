<?php

namespace App\Http\Requests\Api\V1\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateApplicationCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required_without:comment', 'nullable', 'string', 'max:5000'],
            'comment' => ['required_without:content', 'nullable', 'string', 'max:5000'],
        ];
    }
}
