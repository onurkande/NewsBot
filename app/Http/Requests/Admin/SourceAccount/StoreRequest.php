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
            'min_check_interval_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'max_check_interval_minutes' => ['required', 'integer', 'min:1', 'max:1440', 'gte:min_check_interval_minutes'],
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
            'min_check_interval_minutes.required' => 'Minimum kontrol aralığı zorunludur.',
            'max_check_interval_minutes.required' => 'Maksimum kontrol aralığı zorunludur.',
            'max_check_interval_minutes.gte' => 'Maksimum kontrol aralığı minimum değerden küçük olamaz.',
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
            'min_check_interval_minutes' => 'minimum kontrol aralığı',
            'max_check_interval_minutes' => 'maksimum kontrol aralığı',
            'is_active' => 'aktif',
            'notes' => 'notlar',
        ];
    }
}
