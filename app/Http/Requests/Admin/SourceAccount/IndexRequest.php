<?php

namespace App\Http\Requests\Admin\SourceAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'q' => trim((string) $this->input('q', '')),
            'filter' => $this->input('filter', 'all'),
            'sort' => $this->input('sort', 'username'),
            'dir' => strtolower((string) $this->input('dir', 'asc')),
            'per_page' => (int) $this->input('per_page', 15),
        ]);
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'filter' => ['required', Rule::in(['all', 'active', 'passive'])],
            'sort' => ['required', Rule::in(['username', 'trust_score', 'priority_score', 'check_interval', 'last_checked'])],
            'dir' => ['required', Rule::in(['asc', 'desc'])],
            'per_page' => ['required', Rule::in([15, 25, 50, 100])],
        ];
    }

    public function filters(): array
    {
        $data = $this->validated();

        return [
            'q' => $data['q'] ?? '',
            'filter' => $data['filter'] ?? 'all',
            'sort' => $data['sort'] ?? 'username',
            'dir' => $data['dir'] ?? 'asc',
            'per_page' => $data['per_page'] ?? 15,
        ];
    }
}
