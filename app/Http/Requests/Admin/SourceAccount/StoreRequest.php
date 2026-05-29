<?php

namespace App\Http\Requests\Admin\SourceAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'username' => ltrim((string) $this->input('username', ''), '@'),
            'display_name' => trim((string) $this->input('display_name', '')),
            'notes' => trim((string) $this->input('notes', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['nullable', 'exists:source_categories,id'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('source_accounts', 'username'),
            ],
            'display_name' => ['nullable', 'string', 'max:255'],
            'priority_score' => ['required', 'integer', 'min:0', 'max:100'],
            'check_interval_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'trust_score' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Kullanıcı adı zorunludur.',
            'username.unique' => 'Bu kullanıcı adı zaten ekli.',
            'trust_score.required' => 'Güven puanı zorunludur.',
            'priority_score.required' => 'Öncelik puanı zorunludur.',
            'check_interval_minutes.required' => 'Kontrol aralığı zorunludur.',
        ];
    }

    public function attributes(): array
    {
        return [
            'username' => 'kullanıcı adı',
            'display_name' => 'görünen ad',
            'category_id' => 'kategori',
            'trust_score' => 'güven puanı',
            'priority_score' => 'öncelik puanı',
            'check_interval_minutes' => 'kontrol aralığı',
            'is_active' => 'aktif',
            'notes' => 'notlar',
        ];
    }
}
