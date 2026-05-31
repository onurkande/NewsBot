<?php

namespace App\Console\Commands;

use App\Jobs\FetchSourceAccountTweets;
use App\Models\SourceAccount;
use App\Services\NewsCollection\TweetIngestionService;
use App\Services\NewsCollection\TwscrapeClient;
use Illuminate\Console\Command;

class FetchDueSourceAccountsCommand extends Command
{
    protected $signature = 'news:fetch-due-sources {--sync : Queue yerine ayni process icinde calistir} {--limit= : Sadece ilk N kaynagi calistir}';

    protected $description = 'Kontrol zamani gelen X kaynak hesaplarini twscrape ile toplar.';

    public function handle(TwscrapeClient $client, TweetIngestionService $ingestion): int
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
                try {
                    $limit = $account->limited_initial_fetch_pending
                        ? (int) config('news_collection.twscrape.initial_activation_limit')
                        : null;

                    $payload = $client->fetchUserTweets($account, $limit);
                    $result = $ingestion->ingest($account, $payload['tweets'] ?? []);

                    if ($account->limited_initial_fetch_pending) {
                        $account->update(['limited_initial_fetch_pending' => false]);
                    }

                    $this->info("@{$account->username}: {$result['created']} yeni, {$result['skipped']} atlandi.");
                } catch (\Throwable $e) {
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
