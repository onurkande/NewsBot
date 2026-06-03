<?php

namespace App\Http\Requests\Admin\AiQueue;

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
            'q' => ['nullable', 'string'],
            'filter' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
            'dir' => ['nullable', 'string'],
            'per_page' => ['nullable', 'integer'],
        ];
    }

    public function filters(): array
    {
        return $this->only(['q', 'filter', 'sort', 'dir', 'per_page']);
    }
}
