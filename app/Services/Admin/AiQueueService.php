<?php

namespace App\Services\Admin;

use App\Models\AiQueue;
use App\Models\PoolBatch;
use App\Models\PoolBatchItem;
use App\Models\RawTweet;
use App\Services\NewsCollection\AIGenerationLogService;
use Illuminate\Support\Facades\DB;

class AiQueueService
{
    public function __construct(
        private AIGenerationLogService $logService
    ) {}

    public function createFromPoolBatch(PoolBatch $batch): AiQueue
    {
        $selectedItems = PoolBatchItem::query()
            ->where('pool_batch_id', $batch->id)
            ->where('is_selected', true)
            ->get();

        $tweetCount = $selectedItems->count();
        $storyScore = round((float) $selectedItems->avg('final_score'), 2);

        $queue = DB::transaction(function () use ($batch, $tweetCount, $storyScore, $selectedItems) {
            $queue = AiQueue::create([
                'pool_batch_id' => $batch->id,
                'batch_no' => $batch->batch_no,
                'tweet_count' => $tweetCount,
                'story_score' => $storyScore,
                'status' => 'pending',
            ]);

            $tweetIds = $selectedItems->pluck('raw_tweet_id')->all();

            RawTweet::query()
                ->whereIn('id', $tweetIds)
                ->update([
                    'selected_for_ai' => true,
                    'ai_sent_at' => now(),
                ]);

            return $queue;
        });

        $this->logService->logQueue($queue, 'info', 'AI kuyruk kaydi olusturuldu.', [
            'batch_no' => $batch->batch_no,
            'tweet_count' => $tweetCount,
            'story_score' => $storyScore,
        ]);

        return $queue;
    }

    public function markProcessing(AiQueue $queue): AiQueue
    {
        return DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            return $queue->refresh();
        });
    }

    public function markCompleted(AiQueue $queue): AiQueue
    {
        return DB::transaction(function () use ($queue) {
            $queue->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            return $queue->refresh();
        });
    }

    public function markFailed(AiQueue $queue, string $errorMessage): AiQueue
    {
        return DB::transaction(function () use ($queue, $errorMessage) {
            $queue->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
                'completed_at' => now(),
            ]);

            return $queue->refresh();
        });
    }

    public function getPendingQueues(): \Illuminate\Database\Eloquent\Collection
    {
        return AiQueue::query()
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();
    }
}
