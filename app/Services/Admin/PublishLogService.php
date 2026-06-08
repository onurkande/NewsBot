<?php

namespace App\Services\Admin;

use App\Models\PublishLog;
use App\Models\PublishQueue;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

class PublishLogService
{
    public function log(
        PublishQueue $queue,
        string $status,
        ?string $tweetId = null,
        ?string $error = null,
        ?int $duration = null
    ): PublishLog {
        return DB::transaction(function () use ($queue, $status, $tweetId, $error, $duration) {
            return PublishLog::create([
                'publish_queue_id' => $queue->id,
                'ai_generation_id' => $queue->ai_generation_id,
                'publish_account_id' => $queue->publish_account_id,
                'status' => $status,
                'tweet_id_x' => $tweetId,
                'error_message' => $error,
                'duration' => $duration,
                'published_at' => $status === 'published' ? now() : null,
            ]);
        });
    }
}
