<?php

namespace App\Http\Requests\Admin\PoolHistory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:255'],
            'filter' => ['nullable', 'string', 'in:all,completed,failed'],
            'sort' => ['nullable', 'string', 'in:started_at,candidate_count,selected_count,wait_duration_minutes'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'in:15,25,50,100'],
        ];
    }

    public function filters(): array
    {
        return [
            'q' => trim((string) $this->input('q', '')),
            'filter' => (string) Arr::get($this->validated(), 'filter', 'all'),
            'sort' => (string) Arr::get($this->validated(), 'sort', 'started_at'),
            'dir' => (string) Arr::get($this->validated(), 'dir', 'desc'),
            'per_page' => (int) Arr::get($this->validated(), 'per_page', 15),
        ];
    }
}
