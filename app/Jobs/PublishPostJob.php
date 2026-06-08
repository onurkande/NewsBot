<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\PublishQueue;
use App\Models\SystemLog;
use App\Services\Admin\PublishLogService;
use App\Services\Admin\PublishQueueService;
use App\Services\NewsCollection\PublishService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class PublishPostJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function __construct(
        private int $publishQueueId
    ) {}

    public function handle(
        PublishService $publishService,
        PublishQueueService $queueService,
        PublishLogService $logService
    ): void {
        $queue = PublishQueue::query()
            ->with(['aiGeneration.rawTweet', 'publishAccount'])
            ->find($this->publishQueueId);

        if (! $queue) {
            $this->log('warning', 'PublishQueue bulunamadi.', [
                'publish_queue_id' => $this->publishQueueId,
            ]);

            return;
        }

        if ($queue->status !== 'pending') {
            $this->log('info', 'PublishQueue pending durumda degil, atlaniyor.', [
                'publish_queue_id' => $queue->id,
                'status' => $queue->status,
            ]);

            return;
        }

        $queueService->markProcessing($queue);

        $generation = $queue->aiGeneration;
        $account = $queue->publishAccount;
        $rawTweet = $generation?->rawTweet;

        if (! $generation || ! $account) {
            $queueService->markFailed($queue, 'AI Generation veya Publish Account bulunamadi.');
            $logService->log($queue, 'failed', error: 'AI Generation veya Publish Account bulunamadi.');

            return;
        }

        $text = $generation->generated_news ?? '';
        $mediaPaths = $rawTweet?->media_paths ?? [];

        try {
            $result = $publishService->publish($account, $text, $mediaPaths);

            $queueService->markPublished($queue, $result['tweet_id'] ?? '', $result['duration'] ?? 0);
            $logService->log(
                $queue,
                'published',
                tweetId: $result['tweet_id'] ?? null,
                duration: $result['duration'] ?? null
            );
        } catch (Throwable $e) {
            $error = $e->getMessage();

            $queueService->markFailed($queue, $error);
            $logService->log($queue, 'failed', error: $error);

            if ($queue->retry_count < 3) {
                $queue->update([
                    'status' => 'pending',
                    'error_message' => null,
                    'started_at' => null,
                    'completed_at' => null,
                ]);

                $this->log('info', 'Publish retry icin tekrar kuyruga alindi.', [
                    'publish_queue_id' => $queue->id,
                    'retry_count' => $queue->retry_count,
                ]);

                // Job'i tekrar dispatch et (delay ile)
                self::dispatch($this->publishQueueId)
                    ->delay(now()->addMinutes(5));
            } else {
                Alert::create([
                    'alert_type' => 'publish_failed',
                    'title' => 'Yayin Hatasi',
                    'message' => 'Tweet yayini basarisiz oldu: ' . $error,
                    'severity' => 'error',
                    'sent_via' => 'dashboard',
                ]);
            }
        }
    }

    public function failed(Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('PublishPostJob basarisiz oldu', [
            'publish_queue_id' => $this->publishQueueId,
            'error' => $exception->getMessage(),
        ]);

        $queue = PublishQueue::query()->find($this->publishQueueId);

        SystemLog::create([
            'level' => 'error',
            'module' => 'publish',
            'message' => 'Yayin isi basarisiz oldu.',
            'context_json' => [
                'publish_queue_id' => $this->publishQueueId,
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'publish',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
