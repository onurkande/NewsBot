<?php

namespace App\Services\NewsCollection;

class OpenCodeProvider implements AIProvider
{
    public function generate(string $prompt, ?string $model, int $timeout, int $retries): array
    {
        $settings = \App\Models\AiSetting::singleton();
        $apiKey = $settings->opencode_api_key;

        if (! $apiKey) {
            throw new \RuntimeException('OpenCode API key tanimlanmamis. Lutfen AI Ayarlari sayfasindan ekleyin.');
        }

        $baseUrl = $settings->opencode_base_url ?: 'https://opencode.ai/zen/go/v1';
        $modelName = $model ?: $settings->opencode_model ?: 'deepseek-v4-flash';
        $endpoint = rtrim($baseUrl, '/') . '/chat/completions';

        $client = new \GuzzleHttp\Client([
            'timeout' => $timeout,
        ]);

        $lastError = null;

        for ($attempt = 0; $attempt <= $retries; $attempt++) {
            try {
                $response = $client->post($endpoint, [
                    'headers' => [
                        'Authorization' => "Bearer {$apiKey}",
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'model' => $modelName,
                        'messages' => [
                            ['role' => 'user', 'content' => $prompt],
                        ],
                        'temperature' => 0.2,
                        'max_tokens' => 2000,
                    ],
                ]);

                $body = json_decode((string) $response->getBody(), true);
                $content = $body['choices'][0]['message']['content'] ?? '';

                return [
                    'raw' => json_encode($body),
                    'content' => $content,
                    'title' => $this->extractTitle($content),
                    'token_usage' => $body['usage'] ?? null,
                    'provider' => 'opencode',
                    'model' => $body['model'] ?? $modelName,
                ];

            } catch (\Throwable $e) {
                $lastError = $e;
                \Illuminate\Support\Facades\Log::warning("OpenCode deneme {$attempt}/{$retries} basarisiz", [
                    'error' => $e->getMessage(),
                    'endpoint' => $endpoint,
                    'model' => $modelName,
                ]);

                if ($attempt < $retries) {
                    sleep(1);
                }
            }
        }

        throw new \RuntimeException('OpenCode uretimi basarisiz: ' . ($lastError?->getMessage() ?? 'bilinmeyen hata'));
    }

    private function extractTitle(string $content): ?string
    {
        if (preg_match('/BASLIK:\s*(.+)/i', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }
}
