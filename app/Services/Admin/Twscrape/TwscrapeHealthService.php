<?php

namespace App\Services\Admin\Twscrape;

use App\Models\TwscrapeAccount;
use App\Models\TwscrapeLog;
use Illuminate\Support\Facades\File;

class TwscrapeHealthService
{
    public function getHealthStats(): array
    {
        $dbPath = config('news_collection.twscrape.accounts_db');
        $dbExists = File::exists($dbPath);
        $dbReadable = $dbExists && is_readable($dbPath);

        $totalAccounts = TwscrapeAccount::count();
        $activeAccounts = TwscrapeAccount::where('is_active', true)->count();
        $passiveAccounts = $totalAccounts - $activeAccounts;
        $errorAccounts = TwscrapeAccount::where('error_count', '>', 0)->count();

        $lastScrape = TwscrapeAccount::max('last_success_at');
        $lastError = TwscrapeAccount::max('last_error_at');
        
        $lastHealthCheckLog = TwscrapeLog::where('type', 'health_check')->latest()->first();

        return [
            'db_exists' => $dbExists,
            'db_readable' => $dbReadable,
            'db_path' => $dbPath,
            'total_accounts' => $totalAccounts,
            'active_accounts' => $activeAccounts,
            'passive_accounts' => $passiveAccounts,
            'error_accounts' => $errorAccounts,
            'last_scrape_at' => $lastScrape,
            'last_error_at' => $lastError,
            'last_health_check_at' => $lastHealthCheckLog ? $lastHealthCheckLog->created_at : null,
        ];
    }
}
