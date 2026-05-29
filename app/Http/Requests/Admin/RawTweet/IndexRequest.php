<?php

namespace App\Http\Requests\Admin\RawTweet;

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
            'filter' => ['nullable', 'string', 'in:all,processed,pending'],
            'sort' => ['nullable', 'string', 'in:tweeted_at,interactions'],
            'dir' => ['nullable', 'string', 'in:asc,desc'],
            'per_page' => ['nullable', 'integer', 'in:15,25,50,100'],
        ];
    }

    public function filters(): array
    {
        return [
            'q' => $this->string('q')->toString(),
            'filter' => $this->string('filter', 'all')->toString(),
            'sort' => $this->string('sort', 'tweeted_at')->toString(),
            'dir' => $this->string('dir', 'desc')->toString(),
            'per_page' => $this->integer('per_page', 15),
        ];
    }
}
