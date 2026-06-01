<?php

namespace App\Services\Admin;

use App\Models\PoolSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PoolSettingService
{
    public function update(PoolSetting $settings, array $data): PoolSetting
    {
        return DB::transaction(function () use ($settings, $data) {
            $settings->update($this->normalize($data));

            return $settings->refresh();
        });
    }

    private function normalize(array $data): array
    {
        $tweetWindowMin = (int) Arr::get($data, 'tweet_window_min', 10);
        $tweetWindowMax = max($tweetWindowMin, (int) Arr::get($data, 'tweet_window_max', 40));

        $selectionIntervalMin = (int) Arr::get($data, 'selection_interval_min', 10);
        $selectionIntervalMax = max($selectionIntervalMin, (int) Arr::get($data, 'selection_interval_max', 15));

        $tweetCountMin = (int) Arr::get($data, 'tweet_count_min', 3);
        $tweetCountMax = max($tweetCountMin, (int) Arr::get($data, 'tweet_count_max', 5));

        return [
            'tweet_window_min' => $tweetWindowMin,
            'tweet_window_max' => $tweetWindowMax,
            'selection_interval_min' => $selectionIntervalMin,
            'selection_interval_max' => $selectionIntervalMax,
            'tweet_count_min' => $tweetCountMin,
            'tweet_count_max' => $tweetCountMax,
            'is_active' => (bool) Arr::get($data, 'is_active', false),
        ];
    }
}
