<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Models\SourceAccount;
use App\Models\SystemLog;
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

    public function handle(TwscrapeClient $client, TweetIngestionService $ingestion): void
    {
        $account = SourceAccount::findOrFail($this->sourceAccountId);
        $payload = $client->fetchUserTweets($account);

        $ingestion->ingest($account, $payload['tweets'] ?? []);
    }

    public function failed(Throwable $exception): void
    {
        $account = SourceAccount::find($this->sourceAccountId);

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
