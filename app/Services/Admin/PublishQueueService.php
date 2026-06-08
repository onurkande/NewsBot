<?php

namespace App\Services\Admin;

use App\Models\PublishQueue;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

class PublishQueueService
{

    public function markProcessing(PublishQueue $queue): void
    {
        $queue->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        $queue->aiGeneration?->update(['status' => 'publishing']);

        $this->log('info', 'Yayin islemi basladi.', [
            'publish_queue_id' => $queue->id,
            'ai_generation_id' => $queue->ai_generation_id,
        ]);
    }

    public function markPublished(PublishQueue $queue, string $tweetId, int $duration): void
    {
        DB::transaction(function () use ($queue, $tweetId, $duration) {
            $queue->update([
                'status' => 'published',
                'completed_at' => now(),
                'tweet_id_x' => $tweetId,
                'duration' => $duration,
            ]);

            $queue->aiGeneration?->update(['status' => 'published']);

            $this->log('info', 'Yayin basarili.', [
                'publish_queue_id' => $queue->id,
                'tweet_id_x' => $tweetId,
                'duration_ms' => $duration,
            ]);
        });
    }

    public function markFailed(PublishQueue $queue, string $error): void
    {
        DB::transaction(function () use ($queue, $error) {
            $queue->update([
                'status' => 'failed',
                'completed_at' => now(),
                'error_message' => $error,
                'retry_count' => $queue->retry_count + 1,
            ]);

            $queue->aiGeneration?->update(['status' => 'publish_failed']);

            $this->log('error', 'Yayin basarisiz.', [
                'publish_queue_id' => $queue->id,
                'error' => $error,
                'retry_count' => $queue->retry_count,
            ]);
        });
    }

    public function retry(PublishQueue $queue): void
    {
        if ($queue->status !== 'failed') {
            throw new \RuntimeException("Sadece 'failed' durumundaki kayitlar retry edilebilir.");
        }

        $queue->update([
            'status' => 'pending',
            'error_message' => null,
            'started_at' => null,
            'completed_at' => null,
            'tweet_id_x' => null,
            'duration' => null,
        ]);

        $queue->aiGeneration?->update(['status' => 'approved']);

        $this->log('info', 'Yayin retry edildi.', [
            'publish_queue_id' => $queue->id,
            'ai_generation_id' => $queue->ai_generation_id,
        ]);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'publish_queue',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
