<?php

namespace App\Http\Requests\Admin\SourceAccount;

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
            'selected' => ['required', 'array', 'min:1'],
            'selected.*' => ['integer', 'distinct', 'exists:source_accounts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'selected.required' => 'En az bir kayıt seçmelisiniz.',
            'selected.*.exists' => 'Seçili kayıt bulunamadı.',
        ];
    }

    public function attributes(): array
    {
        return [
            'selected' => 'seçili kayıtlar',
        ];
    }
}
