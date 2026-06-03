<?php

namespace App\Http\Requests\Admin\Prompt;

use App\Services\NewsCollection\PromptResolverService;
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
            'name' => ['required', 'string', 'max:255'],
            'prompt_text' => [
                'required', 'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    $invalid = PromptResolverService::validatePlaceholders((string) $value);
                    if ($invalid !== []) {
                        $fail("Geçersiz placeholder(lar): {" . implode('}, {', $invalid) . "}. Kullanilabilir: {tweet_content}, {tweet_count}, {sources}, {total_score}, {first_tweet}");
                    }
                },
            ],
            'source_category_id' => ['nullable', 'integer', 'exists:source_categories,id'],
            'version' => ['required', 'integer', 'min:1'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
