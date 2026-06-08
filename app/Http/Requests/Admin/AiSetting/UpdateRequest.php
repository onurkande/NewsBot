<?php

namespace App\Http\Requests\Admin\AiSetting;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'in:gpt4free,opencode'],
            'model_name' => ['nullable', 'string'],
            'retry_count' => ['required', 'integer', 'min:1', 'max:10'],
            'timeout' => ['required', 'integer', 'min:10', 'max:3600'],
            'concurrent_jobs' => ['required', 'integer', 'min:1', 'max:10'],
            'active_prompt_id' => ['nullable', 'integer', 'exists:prompts,id'],
            'opencode_api_key' => ['nullable', 'string'],
            'opencode_base_url' => ['nullable', 'string', 'url'],
            'opencode_model' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'auto_approve' => ['sometimes', 'boolean'],
            'auto_publish' => ['sometimes', 'boolean'],
            'publish_delay' => ['sometimes', 'integer', 'min:0', 'max:1440'],
        ];
    }
}
