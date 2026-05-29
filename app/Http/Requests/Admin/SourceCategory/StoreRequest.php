<?php

namespace App\Http\Requests\Admin\SourceCategory;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name', ''));
        $slug = trim((string) $this->input('slug', ''));

        $this->merge([
            'name' => $name,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'description' => trim((string) $this->input('description', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('source_categories', 'slug')->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Kategori adı zorunludur.',
            'slug.unique' => 'Bu slug zaten kullanılıyor.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'kategori adı',
            'slug' => 'slug',
            'description' => 'açıklama',
        ];
    }
}
