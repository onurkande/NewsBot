<?php

namespace App\Services\NewsCollection;

interface AIProvider
{
    public function generate(string $prompt, ?string $model, int $timeout, int $retries): array;
}
