<?php

namespace App\Services\Admin;

use App\Models\Prompt;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PromptService
{
    public function create(array $data): Prompt
    {
        return DB::transaction(function () use ($data) {
            return Prompt::create($this->normalize($data));
        });
    }

    public function update(Prompt $prompt, array $data): Prompt
    {
        return DB::transaction(function () use ($prompt, $data) {
            $prompt->update($this->normalize($data));

            return $prompt->refresh();
        });
    }

    public function delete(Prompt $prompt): void
    {
        DB::transaction(function () use ($prompt) {
            $prompt->delete();
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
            return Prompt::query()
                ->whereKey($ids)
                ->delete();
        });
    }

    public function activate(Prompt $prompt): Prompt
    {
        return DB::transaction(function () use ($prompt) {
            $query = Prompt::query()
                ->where('id', '!=', $prompt->id);

            if ($prompt->source_category_id) {
                $query->where('source_category_id', $prompt->source_category_id);
            } else {
                $query->whereNull('source_category_id');
            }

            $query->update(['is_active' => false]);

            $prompt->update(['is_active' => true]);

            return $prompt->refresh();
        });
    }

    public function deactivate(Prompt $prompt): Prompt
    {
        return DB::transaction(function () use ($prompt) {
            $prompt->update(['is_active' => false]);

            return $prompt->refresh();
        });
    }

    private function normalize(array $data): array
    {
        return [
            'name' => (string) Arr::get($data, 'name', ''),
            'prompt_text' => (string) Arr::get($data, 'prompt_text', ''),
            'source_category_id' => Arr::get($data, 'source_category_id') ?: null,
            'version' => (int) Arr::get($data, 'version', 1),
            'is_active' => (bool) Arr::get($data, 'is_active', false),
        ];
    }
}
