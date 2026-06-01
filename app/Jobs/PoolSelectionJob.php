<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\PoolSetting;
use App\Models\SystemLog;
use App\Services\NewsCollection\PoolSelectionService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class PoolSelectionJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function handle(PoolSelectionService $poolSelectionService): void
    {
        $settings = PoolSetting::singleton();

        // next_run_at gecmiste veya bossa calistir
        if ($settings->next_run_at && $settings->next_run_at->isFuture()) {
            return;
        }

        $poolSelectionService->executeSafe();
    }

    public function failed(Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('PoolSelectionJob basarisiz oldu', [
            'error' => $exception->getMessage(),
        ]);

        SystemLog::create([
            'level' => 'error',
            'module' => 'pool_selection',
            'message' => 'Havuz secim isi basarisiz oldu.',
            'context_json' => [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ],
        ]);

        Alert::create([
            'alert_type' => 'pool_selection_failed',
            'title' => 'Havuz secim hatasi',
            'message' => 'Tweet havuzu secim islemi basarisiz oldu: ' . $exception->getMessage(),
            'severity' => 'error',
            'sent_via' => 'dashboard',
        ]);
    }
}
