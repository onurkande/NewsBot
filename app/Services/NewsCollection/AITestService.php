<?php

namespace App\Services\NewsCollection;

use App\Models\AiSetting;

class AITestService
{
    public function __construct(
        private AIProviderFactory $providerFactory
    ) {}

    public function test(string $provider, string $prompt): array
    {
        $settings = AiSetting::singleton();
        $timeout = $settings->timeout;

        $startTime = microtime(true);

        try {
            $aiProvider = $this->providerFactory->make($provider);

            $model = match ($provider) {
                'opencode' => $settings->opencode_model,
                default => $settings->model_name,
            };

            $response = $aiProvider->generate($prompt, $model, $timeout, 0);

            $duration = (int) round((microtime(true) - $startTime) * 1000);

            return [
                'success' => true,
                'content' => mb_substr($response['content'] ?? '', 0, 500),
                'model' => $response['model'] ?? null,
                'provider' => $response['provider'] ?? $provider,
                'duration_ms' => $duration,
            ];

        } catch (\Throwable $e) {
            $duration = (int) round((microtime(true) - $startTime) * 1000);

            \Illuminate\Support\Facades\Log::error('AI baglanti testi basarisiz oldu', [
                'provider' => $provider,
                'error' => $e->getMessage(),
                'duration_ms' => $duration,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'duration_ms' => $duration,
            ];
        }
    }
}
