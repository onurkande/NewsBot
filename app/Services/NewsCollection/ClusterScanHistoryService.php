<?php

namespace App\Services\NewsCollection;

use App\Models\ClusterScanHistory;
use App\Models\SourceAccount;
use Illuminate\Support\Carbon;

class ClusterScanHistoryService
{
    public function record(SourceAccount $account, array $data): void
    {
        $clusterIds = collect($data['story_cluster_ids'] ?? [])
            ->filter()
            ->unique()
            ->values();

        if ($clusterIds->isEmpty()) {
            $clusterIds = collect([null]);
        }

        foreach ($clusterIds as $clusterId) {
            ClusterScanHistory::create([
                'story_cluster_id' => $clusterId,
                'source_account_id' => $account->id,
                'scanned_at' => $data['scanned_at'] ?? now(),
                'previous_scan_elapsed_minutes' => array_key_exists('previous_scan_elapsed_minutes', $data)
                    ? $data['previous_scan_elapsed_minutes']
                    : $this->elapsedMinutes($account->last_checked_at),
                'fetched_tweet_count' => (int) ($data['fetched_tweet_count'] ?? 0),
                'new_tweet_count' => (int) ($data['new_tweet_count'] ?? 0),
                'processed_tweet_count' => (int) ($data['processed_tweet_count'] ?? 0),
                'skipped_tweet_count' => (int) ($data['skipped_tweet_count'] ?? 0),
                'last_tweet_id' => $data['last_tweet_id'] ?? null,
                'duration_ms' => $data['duration_ms'] ?? null,
                'status' => $data['status'] ?? 'success',
                'has_error' => (bool) ($data['has_error'] ?? false),
                'error_message' => $data['error_message'] ?? null,
                'next_check_interval_minutes' => $account->next_check_interval_minutes,
                'next_check_at' => $account->next_check_at,
            ]);
        }
    }

    private function elapsedMinutes(?Carbon $lastCheckedAt): ?int
    {
        return $lastCheckedAt ? max(0, (int) $lastCheckedAt->diffInMinutes(now())) : null;
    }
}
