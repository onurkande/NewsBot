<?php

namespace App\Services\NewsCollection;

class AIProviderFactory
{
    public function make(string $provider): AIProvider
    {
        $providerClass = match ($provider) {
            'gpt4free' => Gpt4freeProvider::class,
            'opencode' => OpenCodeProvider::class,
            default => Gpt4freeProvider::class,
        };

        return app($providerClass);
    }
}
