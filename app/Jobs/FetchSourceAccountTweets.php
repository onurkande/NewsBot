<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\SourceAccount;
use App\Models\SystemLog;
use App\Services\NewsCollection\ClusterScanHistoryService;
use App\Services\NewsCollection\SourceAccountScheduleService;
use App\Services\NewsCollection\TweetIngestionService;
use App\Services\NewsCollection\TwscrapeClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class FetchSourceAccountTweets implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(public int $sourceAccountId)
    {
    }

    public function handle(
        TwscrapeClient $client,
        TweetIngestionService $ingestion,
        SourceAccountScheduleService $schedule,
        ClusterScanHistoryService $history
    ): void
    {
        $account = SourceAccount::findOrFail($this->sourceAccountId);
        $startedAt = microtime(true);
        $scannedAt = now();
        $previousScanElapsedMinutes = $account->last_checked_at
            ? max(0, (int) $account->last_checked_at->diffInMinutes($scannedAt))
            : null;
        $limit = $account->limited_initial_fetch_pending
            ? (int) config('news_collection.twscrape.initial_activation_limit')
            : null;

        try {
            $payload = $client->fetchUserTweets($account, $limit);
            $tweets = $payload['tweets'] ?? [];
            $result = $ingestion->ingest($account, $tweets);

            if ($account->limited_initial_fetch_pending) {
                $account->update(['limited_initial_fetch_pending' => false]);
            }

            $account = $schedule->scheduleNext($account->refresh());

            $history->record($account, [
                'scanned_at' => $scannedAt,
                'fetched_tweet_count' => count($tweets),
                'new_tweet_count' => $result['created'],
                'processed_tweet_count' => $result['processed'],
                'skipped_tweet_count' => $result['skipped'],
                'last_tweet_id' => $result['last_tweet_id'],
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'previous_scan_elapsed_minutes' => $previousScanElapsedMinutes,
                'status' => 'success',
                'has_error' => false,
                'story_cluster_ids' => $result['story_cluster_ids'],
            ]);
        } catch (Throwable $exception) {
            $account = $schedule->scheduleNext($account->refresh());

            $history->record($account, [
                'scanned_at' => $scannedAt,
                'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                'previous_scan_elapsed_minutes' => $previousScanElapsedMinutes,
                'status' => 'failed',
                'has_error' => true,
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function failed(Throwable $exception): void
    {
        $account = SourceAccount::find($this->sourceAccountId);

        \Illuminate\Support\Facades\Log::error('FetchSourceAccountTweets basarisiz oldu', [
            'source_account_id' => $this->sourceAccountId,
            'username' => $account?->username,
            'error' => $exception->getMessage(),
        ]);

        SystemLog::create([
            'level' => 'error',
            'module' => 'twscrape',
            'message' => 'Tweet toplama isi basarisiz oldu.',
            'context_json' => [
                'source_account_id' => $this->sourceAccountId,
                'username' => $account?->username,
                'error' => $exception->getMessage(),
            ],
        ]);

        Alert::create([
            'alert_type' => 'twscrape_fetch_failed',
            'title' => 'Tweet toplama hatasi',
            'message' => ($account?->username ?: 'Bilinmeyen kaynak').' icin twscrape toplama isi basarisiz oldu: '.$exception->getMessage(),
            'severity' => 'error',
            'sent_via' => 'dashboard',
        ]);
    }
}
