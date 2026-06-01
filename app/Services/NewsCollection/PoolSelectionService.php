<?php

namespace App\Services\NewsCollection;

use App\Models\PoolBatch;
use App\Models\PoolBatchItem;
use App\Models\PoolSetting;
use App\Models\RawTweet;
use App\Models\SystemLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class PoolSelectionService
{
    public function __construct(
        private PoolScoringService $scoringService
    ) {
    }

    /**
     * Havuz secim dongusunu calistirir.
     */
    public function execute(): void
    {
        $settings = PoolSetting::singleton();

        if (! $settings->is_active) {
            $this->log('info', 'Havuz secimi pasif durumda, atlaniyor.');

            return;
        }

        $startedAt = now();
        $tweetWindow = random_int($settings->tweet_window_min, $settings->tweet_window_max);
        $candidateWindow = now()->subMinutes($tweetWindow);

        // 1. Aday tweetleri al (daha once secilmemis olanlar)
        $candidates = RawTweet::query()
            ->with('sourceAccount')
            ->where('selected_for_pool', false)
            ->where('tweeted_at', '>=', $candidateWindow)
            ->orderByDesc('tweeted_at')
            ->get();

        $candidateCount = $candidates->count();

        if ($candidateCount === 0) {
            $this->log('info', 'Aday tweet bulunamadi.', [
                'tweet_window_minutes' => $tweetWindow,
            ]);
            $this->scheduleNextRun($settings);

            return;
        }

        // 2. Puanla
        $scored = $this->scoringService->scoreCollection($candidates);

        // 3. Sirala
        $sorted = $scored->sortByDesc('final_score')->values();

        // 4. Kac tweet secilecek?
        $maxSelectable = min(
            random_int($settings->tweet_count_min, $settings->tweet_count_max),
            $candidateCount
        );

        $selected = $sorted->slice(0, $maxSelectable);
        $selectedTweetIds = $selected->pluck('tweet.id')->all();

        // 5. Bir sonraki calisma zamani
        $waitDuration = random_int($settings->selection_interval_min, $settings->selection_interval_max);
        $nextRunAt = now()->addMinutes($waitDuration);

        // 6. Batch no uret
        $batchNo = $this->generateBatchNo();

        DB::transaction(function () use ($sorted, $selectedTweetIds, $batchNo, $tweetWindow, $candidateCount, $maxSelectable, $waitDuration, $nextRunAt, $startedAt) {
            // Batch kaydet
            $batch = PoolBatch::create([
                'batch_no' => $batchNo,
                'tweet_window_minutes' => $tweetWindow,
                'candidate_count' => $candidateCount,
                'selected_count' => count($selectedTweetIds),
                'wait_duration_minutes' => $waitDuration,
                'next_run_at' => $nextRunAt,
                'started_at' => $startedAt,
                'completed_at' => now(),
                'status' => 'completed',
                'error_message' => null,
            ]);

            // Batch item'lari kaydet (tum adaylar)
            foreach ($sorted as $index => $item) {
                PoolBatchItem::create([
                    'pool_batch_id' => $batch->id,
                    'raw_tweet_id' => $item['tweet']->id,
                    'priority_score' => $item['priority_score'],
                    'engagement_score' => $item['engagement_score'],
                    'final_score' => $item['final_score'],
                    'is_selected' => in_array($item['tweet']->id, $selectedTweetIds, true),
                    'rank' => $index + 1,
                ]);
            }

            // Secilen tweetleri isaretle (bir daha secilememesi icin)
            RawTweet::query()
                ->whereIn('id', $selectedTweetIds)
                ->update([
                    'selected_for_pool' => true,
                    'selected_at' => now(),
                ]);
        });

        // 7. Ayarlarin next_run_at guncelle
        $settings->update(['next_run_at' => $nextRunAt]);

        $this->log('info', 'Havuz secimi tamamlandi.', [
            'batch_no' => $batchNo,
            'candidate_count' => $candidateCount,
            'selected_count' => count($selectedTweetIds),
            'tweet_window_minutes' => $tweetWindow,
            'wait_duration_minutes' => $waitDuration,
            'next_run_at' => $nextRunAt->toDateTimeString(),
        ]);
    }

    /**
     * Havuz secim dongusunu calistirir, hata olursa loglar ve batch'i failed olarak kaydeder.
     */
    public function executeSafe(): void
    {
        $settings = PoolSetting::singleton();
        $startedAt = now();
        $batchNo = $this->generateBatchNo();

        try {
            $this->execute();
        } catch (Throwable $exception) {
            $waitDuration = random_int($settings->selection_interval_min, $settings->selection_interval_max);
            $nextRunAt = now()->addMinutes($waitDuration);

            PoolBatch::create([
                'batch_no' => $batchNo,
                'tweet_window_minutes' => 0,
                'candidate_count' => 0,
                'selected_count' => 0,
                'wait_duration_minutes' => $waitDuration,
                'next_run_at' => $nextRunAt,
                'started_at' => $startedAt,
                'completed_at' => now(),
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
            ]);

            $settings->update(['next_run_at' => $nextRunAt]);

            $this->log('error', 'Havuz secimi basarisiz oldu.', [
                'batch_no' => $batchNo,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    private function scheduleNextRun(PoolSetting $settings): void
    {
        $waitDuration = random_int($settings->selection_interval_min, $settings->selection_interval_max);
        $nextRunAt = now()->addMinutes($waitDuration);
        $settings->update(['next_run_at' => $nextRunAt]);
    }

    private function generateBatchNo(): string
    {
        $prefix = 'B-' . now()->format('Ymd');
        $lastToday = PoolBatch::query()
            ->where('batch_no', 'like', $prefix . '-%')
            ->orderByDesc('batch_no')
            ->value('batch_no');

        $sequence = 1;

        if ($lastToday) {
            $parts = explode('-', $lastToday);
            $lastSeq = (int) end($parts);
            $sequence = $lastSeq + 1;
        }

        return sprintf('%s-%03d', $prefix, $sequence);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'pool_selection',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
