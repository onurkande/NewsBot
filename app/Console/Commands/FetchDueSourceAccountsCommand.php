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
                $payload = $client->fetchUserTweets($account);
                $result = $ingestion->ingest($account, $payload['tweets'] ?? []);
                $this->info("@{$account->username}: {$result['created']} yeni, {$result['skipped']} atlandi.");
            } else {
                FetchSourceAccountTweets::dispatch($account->id);
                $this->info("@{$account->username} queue'ya eklendi.");
            }
        }

        return self::SUCCESS;
    }
}
