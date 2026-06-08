<?php

namespace App\Services\Admin;

use App\Models\PublishSettings;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PublishSettingsService
{
    public function update(PublishSettings $settings, array $data): PublishSettings
    {
        return DB::transaction(function () use ($settings, $data) {
            $settings->update($this->normalize($data));
            return $settings->refresh();
        });
    }

    private function normalize(array $data): array
    {
        return [
            'daily_post_limit' => max(1, (int) Arr::get($data, 'daily_post_limit', 10)),
            'hourly_post_limit' => max(1, (int) Arr::get($data, 'hourly_post_limit', 1)),
            'min_delay_minutes' => max(0, (int) Arr::get($data, 'min_delay_minutes', 45)),
            'max_delay_minutes' => max(0, (int) Arr::get($data, 'max_delay_minutes', 90)),
            'publish_start_hour' => max(0, min(23, (int) Arr::get($data, 'publish_start_hour', 8))),
            'publish_end_hour' => max(0, min(24, (int) Arr::get($data, 'publish_end_hour', 23))),
            'min_gap_minutes' => max(0, (int) Arr::get($data, 'min_gap_minutes', 3)),
            'auto_publish_enabled' => (bool) Arr::get($data, 'auto_publish_enabled', false),
            'auto_scale_enabled' => (bool) Arr::get($data, 'auto_scale_enabled', false),
            'follower_scale_enabled' => (bool) Arr::get($data, 'follower_scale_enabled', false),
            'engagement_scale_enabled' => (bool) Arr::get($data, 'engagement_scale_enabled', false),
            'warmup_mode_enabled' => (bool) Arr::get($data, 'warmup_mode_enabled', false),
            'warmup_0_30_daily_limit' => max(1, (int) Arr::get($data, 'warmup_0_30_daily_limit', 3)),
            'warmup_30_60_daily_limit' => max(1, (int) Arr::get($data, 'warmup_30_60_daily_limit', 5)),
            'warmup_60_plus_daily_limit' => max(1, (int) Arr::get($data, 'warmup_60_plus_daily_limit', 10)),
            'publish_expiration_hours' => max(0, (int) Arr::get($data, 'publish_expiration_hours', 6)),
        ];
    }
}
