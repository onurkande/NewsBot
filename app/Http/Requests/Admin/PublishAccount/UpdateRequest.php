<?php

namespace App\Http\Requests\Admin\PublishAccount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $accountId = $this->route('publish_account')?->id;

        return [
            'username' => ['required', 'string', 'max:100', "unique:publish_accounts,username,{$accountId}"],
            'display_name' => ['nullable', 'string', 'max:100'],
            'auth_token' => ['required', 'string', 'max:500'],
            'ct0' => ['required', 'string', 'max:500'],
            'cookies_json' => ['nullable', 'json'],
            'account_created_at' => ['nullable', 'date'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
