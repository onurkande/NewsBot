<?php

namespace App\Http\Requests\Admin\PublishAccount;

use Illuminate\Foundation\Http\FormRequest;

class IndexRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'q' => ['nullable', 'string', 'max:100'],
            'filter' => ['nullable', 'string', 'in:all,active,inactive'],
            'sort' => ['nullable', 'string', 'in:username,created_at'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'in:15,25,50,100'],
        ];
    }

    public function filters(): array
    {
        return $this->only(['q', 'filter', 'sort', 'dir', 'per_page']);
    }
}
