<?php

namespace App\Jobs;

use App\Models\RawTweet;
use App\Models\SystemLog;
use App\Services\NewsCollection\TweetMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class DownloadTweetMediaJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        private int $rawTweetId
    ) {}

    public function handle(TweetMediaService $mediaService): void
    {
        $tweet = RawTweet::query()->find($this->rawTweetId);

        if (! $tweet) {
            SystemLog::create([
                'level' => 'warning',
                'module' => 'media_management',
                'message' => 'Medya indirme isi atlandi: Tweet bulunamadi.',
                'context_json' => ['raw_tweet_id' => $this->rawTweetId, 'event' => 'media_download_failed'],
            ]);

            return;
        }

        $mediaService->downloadForTweet($tweet);
    }

    public function failed(Throwable $exception): void
    {
        SystemLog::create([
            'level' => 'error',
            'module' => 'media_management',
            'message' => 'Medya indirme isi basarisiz oldu.',
            'context_json' => [
                'raw_tweet_id' => $this->rawTweetId,
                'event' => 'media_download_failed',
                'error' => $exception->getMessage(),
            ],
        ]);
    }
}
