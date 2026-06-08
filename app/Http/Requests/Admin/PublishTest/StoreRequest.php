<?php

namespace App\Http\Requests\Admin\PublishTest;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string', 'min:1', 'max:1000'],
            'media_paths' => ['nullable', 'string'],
            'publish_account_id' => ['required', 'integer', 'exists:publish_accounts,id'],
        ];
    }
}
