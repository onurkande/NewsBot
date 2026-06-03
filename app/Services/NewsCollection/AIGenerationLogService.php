<?php

namespace App\Services\NewsCollection;

use App\Models\AiGenerationLog;

class AIGenerationLogService
{
    public function logQueue(\App\Models\AiQueue $queue, string $level, string $message, array $context = []): AiGenerationLog
    {
        return AiGenerationLog::create([
            'ai_queue_id' => $queue->id,
            'ai_generation_id' => null,
            'level' => $level,
            'message' => $message,
            'context_json' => $context,
        ]);
    }

    public function logGeneration(\App\Models\AiGeneration $generation, string $level, string $message, array $context = []): AiGenerationLog
    {
        return AiGenerationLog::create([
            'ai_queue_id' => $generation->ai_queue_id,
            'ai_generation_id' => $generation->id,
            'level' => $level,
            'message' => $message,
            'context_json' => $context,
        ]);
    }

    public function logInfo(\App\Models\AiGeneration $generation, string $message, array $context = []): AiGenerationLog
    {
        return $this->logGeneration($generation, 'info', $message, $context);
    }

    public function logWarning(\App\Models\AiGeneration $generation, string $message, array $context = []): AiGenerationLog
    {
        return $this->logGeneration($generation, 'warning', $message, $context);
    }

    public function logError(\App\Models\AiGeneration $generation, string $message, array $context = []): AiGenerationLog
    {
        return $this->logGeneration($generation, 'error', $message, $context);
    }

    public function logCritical(\App\Models\AiGeneration $generation, string $message, array $context = []): AiGenerationLog
    {
        return $this->logGeneration($generation, 'critical', $message, $context);
    }
}
