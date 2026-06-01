<?php

namespace App\Console\Commands;

use App\Jobs\FetchSourceAccountTweets;
use App\Models\SourceAccount;
use App\Services\NewsCollection\ClusterScanHistoryService;
use App\Services\NewsCollection\SourceAccountScheduleService;
use App\Services\NewsCollection\TweetIngestionService;
use App\Services\NewsCollection\TwscrapeClient;
use Illuminate\Console\Command;

class FetchDueSourceAccountsCommand extends Command
{
    protected $signature = 'news:fetch-due-sources {--sync : Queue yerine ayni process icinde calistir} {--limit= : Sadece ilk N kaynagi calistir}';

    protected $description = 'Kontrol zamani gelen X kaynak hesaplarini twscrape ile toplar.';

    public function handle(
        TwscrapeClient $client,
        TweetIngestionService $ingestion,
        SourceAccountScheduleService $schedule,
        ClusterScanHistoryService $history
    ): int
    {
        $query = SourceAccount::query()->dueForCheck()->orderByDesc('priority_score');

        if ($this->option('limit')) {
            $query->limit((int) $this->option('limit'));
        }

        $accounts = $query->get();

        if ($accounts->isEmpty()) {
            $this->info('Kontrol zamani gelen kaynak yok.');
            return self::SUCCESS;
        }

        foreach ($accounts as $account) {
            if ($this->option('sync')) {
                $startedAt = microtime(true);
                $scannedAt = now();
                $previousScanElapsedMinutes = $account->last_checked_at
                    ? max(0, (int) $account->last_checked_at->diffInMinutes($scannedAt))
                    : null;

                try {
                    $limit = $account->limited_initial_fetch_pending
                        ? (int) config('news_collection.twscrape.initial_activation_limit')
                        : null;

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

                    $this->info("@{$account->username}: {$result['created']} yeni, {$result['skipped']} atlandi.");
                } catch (\Throwable $e) {
                    $account = $schedule->scheduleNext($account->refresh());

                    $history->record($account, [
                        'scanned_at' => $scannedAt,
                        'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                        'previous_scan_elapsed_minutes' => $previousScanElapsedMinutes,
                        'status' => 'failed',
                        'has_error' => true,
                        'error_message' => $e->getMessage(),
                    ]);

                    \Illuminate\Support\Facades\Log::error("Senkron tweet toplama hatasi (@{$account->username})", [
                        'error' => $e->getMessage()
                    ]);
                    $this->error("@{$account->username} icin islem basarisiz: " . $e->getMessage());
                }
            } else {
                FetchSourceAccountTweets::dispatch($account->id);
                $this->info("@{$account->username} queue'ya eklendi.");
            }
        }

        return self::SUCCESS;
    }
}
