<?php

namespace App\Jobs;

use App\Models\SystemLog;
use App\Services\NewsCollection\TweetMediaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class MediaCleanupJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function handle(TweetMediaService $mediaService): void
    {
        try {
            $mediaService->cleanupExpired();
        } catch (Throwable $e) {
            SystemLog::create([
                'level' => 'error',
                'module' => 'media_management',
                'message' => 'Medya temizlik isi basarisiz oldu.',
                'context_json' => [
                    'event' => 'cleanup_failed',
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }
    }
}
