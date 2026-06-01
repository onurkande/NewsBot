<?php

namespace App\Services\Admin;

use App\Models\SourceAccount;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SourceAccountService
{
    public function create(array $data): SourceAccount
    {
        return DB::transaction(function () use ($data) {
            return SourceAccount::create(array_merge($this->normalize($data), [
                'limited_initial_fetch_pending' => true,
            ]));
        });
    }

    public function update(SourceAccount $account, array $data): SourceAccount
    {
        return DB::transaction(function () use ($account, $data) {
            $normalized = $this->normalize($data);

            if (! $account->is_active && $normalized['is_active']) {
                $normalized['limited_initial_fetch_pending'] = true;
                $normalized['next_check_interval_minutes'] = null;
                $normalized['next_check_at'] = null;
            }

            $account->update($normalized);

            return $account->refresh();
        });
    }

    public function delete(SourceAccount $account): void
    {
        DB::transaction(function () use ($account) {
            $account->delete();
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
            return SourceAccount::query()
                ->whereKey($ids)
                ->delete();
        });
    }

    private function normalize(array $data): array
    {
        $minInterval = (int) Arr::get($data, 'min_check_interval_minutes', 15);
        $maxInterval = max($minInterval, (int) Arr::get($data, 'max_check_interval_minutes', $minInterval));

        return [
            'username' => ltrim((string) Arr::get($data, 'username', ''), '@'),
            'display_name' => $this->nullableTrim(Arr::get($data, 'display_name')),
            'category_id' => Arr::get($data, 'category_id'),
            'priority_score' => (int) Arr::get($data, 'priority_score', 50),
            'trust_score' => (int) Arr::get($data, 'trust_score', 50),
            'check_interval_minutes' => $minInterval,
            'min_check_interval_minutes' => $minInterval,
            'max_check_interval_minutes' => $maxInterval,
            'is_active' => (bool) Arr::get($data, 'is_active', false),
            'notes' => $this->nullableTrim(Arr::get($data, 'notes')),
        ];
    }

    private function nullableTrim(mixed $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }
}
