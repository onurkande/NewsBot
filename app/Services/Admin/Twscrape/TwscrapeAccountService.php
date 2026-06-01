<?php

namespace App\Services\Admin\Twscrape;

use App\Models\TwscrapeAccount;
use App\Models\TwscrapeSqliteAccount;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TwscrapeAccountService
{
    /**
     * Syncs accounts from SQLite (accounts.db) to Laravel's DB.
     */
    public function syncAccounts(): void
    {
        try {
            $sqliteAccounts = TwscrapeSqliteAccount::all();

            foreach ($sqliteAccounts as $sqliteAccount) {
                $account = TwscrapeAccount::firstOrNew(['username' => $sqliteAccount->username]);

                // Update active status based on sqlite if it's the first time
                // or keep laravel's active status. Let's just create if not exists
                // and maybe update the status if there's an error msg.
                
                if (!$account->exists) {
                    $account->is_active = $sqliteAccount->active;
                    $account->weight = 50; // default weight
                    $account->total_usage = 0;
                    $account->error_count = 0;
                    $account->status = $sqliteAccount->active ? 'active' : 'suspended';
                }

                if ($sqliteAccount->error_msg) {
                    $account->last_error_message = $sqliteAccount->error_msg;
                }

                $account->save();
            }
        } catch (\Exception $e) {
            Log::error('Twscrape accounts sync failed: ' . $e->getMessage());
        }
    }

    /**
     * Updates the weight of an account.
     */
    public function updateWeight(string $username, int $weight): bool
    {
        return TwscrapeAccount::where('username', $username)->update(['weight' => $weight]) > 0;
    }

    /**
     * Toggles the active status of an account.
     */
    public function toggleStatus(string $username): bool
    {
        $account = TwscrapeAccount::where('username', $username)->firstOrFail();
        $account->is_active = !$account->is_active;
        return $account->save();
    }

    /**
     * Weighted Round Robin algorithm to select the best account.
     * Score = (Current Time - last_used_at in seconds) * weight.
     * Accounts with null last_used_at get highest priority.
     */
    public function selectAccountForScrape(): ?TwscrapeAccount
    {
        $accounts = TwscrapeAccount::where('is_active', true)->get();

        if ($accounts->isEmpty()) {
            return null;
        }

        $bestAccount = null;
        $maxScore = -1;
        $currentTime = time();

        foreach ($accounts as $account) {
            // If never used, give it the highest possible priority (score = infinity)
            if (is_null($account->last_used_at)) {
                return $account;
            }

            $secondsSinceLastUse = $currentTime - $account->last_used_at->timestamp;
            // Prevent negative time if clock is out of sync
            $secondsSinceLastUse = max(1, $secondsSinceLastUse);

            $score = $secondsSinceLastUse * $account->weight;

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestAccount = $account;
            }
        }

        return $bestAccount;
    }

    /**
     * Mark account usage (success or fail)
     */
    public function markUsage(string $username, bool $isSuccess, ?string $errorMessage = null): void
    {
        $account = TwscrapeAccount::where('username', $username)->first();
        if (!$account) return;

        $account->total_usage += 1;
        $account->last_used_at = now();

        if ($isSuccess) {
            $account->last_success_at = now();
        } else {
            $account->last_error_at = now();
            $account->error_count += 1;
            $account->last_error_message = $errorMessage;
        }

        $account->save();
    }
}
