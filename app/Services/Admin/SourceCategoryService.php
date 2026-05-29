<?php

namespace App\Services\Admin;

use App\Models\SourceCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SourceCategoryService
{
    public function create(array $data): SourceCategory
    {
        return DB::transaction(function () use ($data) {
            return SourceCategory::create($this->normalize($data));
        });
    }

    public function update(SourceCategory $category, array $data): SourceCategory
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update($this->normalize($data));

            return $category->refresh();
        });
    }

    public function delete(SourceCategory $category): void
    {
        DB::transaction(function () use ($category) {
            $category->delete();
        });
    }

    public function bulkDelete(array $ids): int
    {
        $ids = collect($ids)
            ->flatten()
            ->filter()
            ->map(static fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if ($ids === []) {
            return 0;
        }

        return (int) DB::transaction(function () use ($ids) {
            return SourceCategory::query()
                ->whereKey($ids)
                ->delete();
        });
    }

    private function normalize(array $data): array
    {
        $name = trim((string) Arr::get($data, 'name', ''));
        $slug = trim((string) Arr::get($data, 'slug', ''));

        return [
            'name' => $name,
            'slug' => $slug !== '' ? Str::slug($slug) : Str::slug($name),
            'description' => $this->nullableTrim(Arr::get($data, 'description')),
        ];
    }

    private function nullableTrim(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
