<?php

namespace App\Http\Requests\Admin\StoryCluster;

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
            'q' => ['nullable', 'string', 'max:255'],
            'filter' => ['nullable', 'string', 'in:all,open,selected,published,ignored'],
            'sort' => ['nullable', 'string', 'in:last_updated_at,story_score,items_count'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'in:15,25,50,100'],
        ];
    }

    public function filters(): array
    {
        return [
            'q' => $this->string('q')->toString(),
            'filter' => $this->string('filter', 'all')->toString(),
            'sort' => $this->string('sort', 'last_updated_at')->toString(),
            'dir' => $this->string('dir', 'desc')->toString(),
            'per_page' => $this->integer('per_page', 15),
        ];
    }
}
