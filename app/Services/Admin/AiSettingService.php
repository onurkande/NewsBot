<?php

namespace App\Services\Admin;

use App\Models\AiSetting;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class AiSettingService
{
    public function update(AiSetting $settings, array $data): AiSetting
    {
        return DB::transaction(function () use ($settings, $data) {
            $settings->update($this->normalize($data));

            return $settings->refresh();
        });
    }

    private function normalize(array $data): array
    {
        $retryCount = (int) Arr::get($data, 'retry_count', 3);
        $timeout = (int) Arr::get($data, 'timeout', 300);
        $concurrentJobs = (int) Arr::get($data, 'concurrent_jobs', 1);

        $normalized = [
            'provider' => Arr::get($data, 'provider', 'gpt4free'),
            'model_name' => Arr::get($data, 'model_name'),
            'retry_count' => max(1, $retryCount),
            'timeout' => max(10, $timeout),
            'concurrent_jobs' => max(1, min(10, $concurrentJobs)),
            'active_prompt_id' => Arr::get($data, 'active_prompt_id'),
            'is_active' => (bool) Arr::get($data, 'is_active', false),
            'auto_approve' => (bool) Arr::get($data, 'auto_approve', false),
        ];

        if (($data['provider'] ?? '') === 'opencode') {
            $normalized['opencode_api_key'] = Arr::get($data, 'opencode_api_key');
            $normalized['opencode_base_url'] = Arr::get($data, 'opencode_base_url') ?: 'https://opencode.ai/zen/go/v1';
            $normalized['opencode_model'] = Arr::get($data, 'opencode_model') ?: 'deepseek-v4-flash';
        }

        return $normalized;
    }
}
