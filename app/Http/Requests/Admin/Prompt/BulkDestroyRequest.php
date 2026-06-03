<?php

namespace App\Http\Requests\Admin\Prompt;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'selected' => ['required', 'array'],
            'selected.*' => ['integer', 'exists:prompts,id'],
        ];
    }
}
