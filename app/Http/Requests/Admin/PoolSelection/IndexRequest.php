<?php

namespace App\Http\Requests\Admin\PoolSelection;

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
            'filter' => ['nullable', 'string', 'in:all,selected,not_selected'],
            'sort' => ['nullable', 'string', 'in:final_score,priority_score,engagement_score,rank'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'in:15,25,50,100'],
        ];
    }

    public function filters(): array
    {
        return [
            'q' => trim((string) $this->input('q', '')),
            'filter' => (string) Arr::get($this->validated(), 'filter', 'all'),
            'sort' => (string) Arr::get($this->validated(), 'sort', 'final_score'),
            'dir' => (string) Arr::get($this->validated(), 'dir', 'desc'),
            'per_page' => (int) Arr::get($this->validated(), 'per_page', 15),
        ];
    }
}
