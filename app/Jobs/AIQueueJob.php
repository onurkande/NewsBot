<?php

namespace App\Jobs;

use App\Models\AiQueue;
use App\Models\Alert;
use App\Models\PoolBatch;
use App\Models\SystemLog;
use App\Services\Admin\AiQueueService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class AIQueueJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function handle(AiQueueService $queueService): void
    {
        $batches = PoolBatch::query()
            ->where('status', 'completed')
            ->whereDoesntHave('aiQueue')
            ->orderBy('started_at')
            ->get();

        if ($batches->isEmpty()) {
            return;
        }

        foreach ($batches as $batch) {
            $existing = AiQueue::query()
                ->where('pool_batch_id', $batch->id)
                ->exists();

            if ($existing) {
                continue;
            }

            $queue = $queueService->createFromPoolBatch($batch);

            $selectedItems = $batch->items()->where('is_selected', true)->count();

            if ($selectedItems > 0) {
                AIGenerationJob::dispatch($queue->id);
                $this->log('info', "AI kuyrugu olusturuldu ve generation job tetiklendi.", [
                    'batch_no' => $batch->batch_no,
                    'tweet_count' => $selectedItems,
                ]);
            } else {
                $this->log('warning', "Batch'te secilmis tweet yok, AI kuyrugu olusturuldu ama generation atlandi.", [
                    'batch_no' => $batch->batch_no,
                ]);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('AIQueueJob basarisiz oldu', [
            'error' => $exception->getMessage(),
        ]);

        SystemLog::create([
            'level' => 'error',
            'module' => 'ai_queue',
            'message' => 'AI kuyruk isi basarisiz oldu.',
            'context_json' => [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);

        Alert::create([
            'alert_type' => 'ai_queue_failed',
            'title' => 'AI Kuyruk Hatasi',
            'message' => 'AI kuyruk islemi basarisiz oldu: ' . $exception->getMessage(),
            'severity' => 'error',
            'sent_via' => 'dashboard',
        ]);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'ai_queue',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
