<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\SystemLog;
use App\Services\Admin\PublishSchedulerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class PublishSchedulerJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    public function handle(PublishSchedulerService $schedulerService): void
    {
        $this->log('info', 'PublishSchedulerJob calisti.');

        try {
            $schedulerService->processApproved();
            $schedulerService->dispatchPending();
            $this->log('info', 'PublishSchedulerJob tamamlandi.');
        } catch (Throwable $e) {
            $this->log('error', 'PublishSchedulerJob hata ile tamamlandi.', [
                'error' => $e->getMessage(),
            ]);

            Alert::create([
                'alert_type' => 'publish_scheduler_failed',
                'title' => 'Publish Scheduler Hatasi',
                'message' => 'Publish scheduler calistirilamadi: ' . $e->getMessage(),
                'severity' => 'error',
                'sent_via' => 'dashboard',
            ]);
        }
    }

    public function failed(Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('PublishSchedulerJob basarisiz oldu', [
            'error' => $exception->getMessage(),
        ]);

        SystemLog::create([
            'level' => 'error',
            'module' => 'publish_scheduler',
            'message' => 'Publish Scheduler isi basarisiz oldu.',
            'context_json' => [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'publish_scheduler',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
