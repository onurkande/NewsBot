<?php

namespace App\Services\NewsCollection;

use App\Models\RawTweet;
use Illuminate\Support\Collection;

class PoolScoringService
{
    /**
     * Tum aday tweetler icin puan hesaplar.
     *
     * Donus: Collection of arrays with keys:
     *   - tweet: RawTweet model
     *   - priority_score: float (0-100)
     *   - engagement_score: float (0-100)
     *   - final_score: float (0-100)
     */
    public function scoreCollection(Collection $candidates): Collection
    {
        if ($candidates->isEmpty()) {
            return collect();
        }

        $maxEngagementRaw = $this->maxEngagementRaw($candidates);

        return $candidates->map(function (RawTweet $tweet) use ($maxEngagementRaw) {
            $priorityScore = $this->calculatePriorityScore($tweet);
            $engagementRaw = $this->calculateEngagementRaw($tweet);
            $engagementScore = $maxEngagementRaw > 0
                ? round(($engagementRaw / $maxEngagementRaw) * 100, 2)
                : 0.0;

            $finalScore = round(($priorityScore * 0.4) + ($engagementScore * 0.6), 2);

            return [
                'tweet' => $tweet,
                'priority_score' => $priorityScore,
                'engagement_score' => $engagementScore,
                'final_score' => $finalScore,
            ];
        });
    }

    private function calculatePriorityScore(RawTweet $tweet): float
    {
        $source = $tweet->sourceAccount;

        if (! $source) {
            return 50.0;
        }

        return (float) max(0, min(100, $source->priority_score));
    }

    private function calculateEngagementRaw(RawTweet $tweet): float
    {
        $likes = (int) $tweet->like_count;
        $retweets = (int) $tweet->retweet_count;
        $replies = (int) $tweet->reply_count;
        $quotes = (int) $tweet->quote_count;
        $views = (int) $tweet->view_count;

        return ($likes)
            + ($retweets * 2)
            + ($replies * 1.5)
            + ($quotes * 1.2)
            + ($views * 0.001);
    }

    private function maxEngagementRaw(Collection $candidates): float
    {
        return (float) $candidates->map(fn (RawTweet $tweet) => $this->calculateEngagementRaw($tweet))->max();
    }
}
