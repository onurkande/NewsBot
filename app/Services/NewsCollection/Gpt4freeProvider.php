<?php

namespace App\Services\NewsCollection;

class Gpt4freeProvider implements AIProvider
{
    public function __construct(
        private Gpt4freeClient $client
    ) {}

    public function generate(string $prompt, ?string $model, int $timeout, int $retries): array
    {
        $lastError = null;

        for ($attempt = 0; $attempt <= $retries; $attempt++) {
            try {
                return $this->client->generate($prompt, $model, $timeout);

            } catch (\Throwable $e) {
                $lastError = $e;
                \Illuminate\Support\Facades\Log::warning("gpt4free deneme {$attempt}/{$retries} basarisiz", [
                    'error' => $e->getMessage(),
                ]);

                if ($attempt < $retries) {
                    sleep(1);
                }
            }
        }

        throw new \RuntimeException('GPT4Free uretimi basarisiz: ' . ($lastError?->getMessage() ?? 'bilinmeyen hata'));
    }
}
