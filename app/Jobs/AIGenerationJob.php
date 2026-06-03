<?php

namespace App\Jobs;

use App\Models\AiQueue;
use App\Models\Alert;
use App\Models\SystemLog;
use App\Services\NewsCollection\AIGenerationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class AIGenerationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        private int $aiQueueId
    ) {}

    public function handle(AIGenerationService $generationService): void
    {
        $queue = AiQueue::query()->find($this->aiQueueId);

        if (! $queue) {
            $this->log('warning', 'AI kuyrugu bulunamadi.', [
                'ai_queue_id' => $this->aiQueueId,
            ]);

            return;
        }

        if ($queue->status !== 'pending') {
            $this->log('info', 'AI kuyrugu pending durumda degil, atlaniyor.', [
                'ai_queue_id' => $queue->id,
                'status' => $queue->status,
            ]);

            return;
        }

        $generationService->processQueue($queue);
    }

    public function failed(Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('AIGenerationJob basarisiz oldu', [
            'ai_queue_id' => $this->aiQueueId,
            'error' => $exception->getMessage(),
        ]);

        $queue = AiQueue::query()->find($this->aiQueueId);

        SystemLog::create([
            'level' => 'error',
            'module' => 'ai_generation',
            'message' => 'AI uretim isi basarisiz oldu.',
            'context_json' => [
                'ai_queue_id' => $this->aiQueueId,
                'batch_no' => $queue?->batch_no,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);

        Alert::create([
            'alert_type' => 'ai_generation_failed',
            'title' => 'AI Uretim Hatasi',
            'message' => 'AI icerik uretimi basarisiz oldu: ' . $exception->getMessage(),
            'severity' => 'error',
            'sent_via' => 'dashboard',
        ]);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'ai_generation',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
