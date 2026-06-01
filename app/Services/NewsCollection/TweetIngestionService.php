<?php

namespace App\Services\NewsCollection;

use App\Models\DuplicateCheck;
use App\Models\RawTweet;
use App\Models\SourceAccount;
use App\Models\StoryCluster;
use App\Models\StoryClusterItem;
use App\Models\StoryScore;
use App\Models\SystemLog;
use App\Models\TweetNormalizedText;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TweetIngestionService
{
    public function ingest(SourceAccount $account, array $tweets): array
    {
        $created = 0;
        $skipped = 0;
        $clusters = 0;
        $processed = 0;
        $clusterIds = [];

        DB::transaction(function () use ($account, $tweets, &$created, &$skipped, &$clusters, &$processed, &$clusterIds) {
            foreach ($tweets as $tweetData) {
                $tweetId = (string) ($tweetData['tweet_id'] ?? '');

                if ($tweetId === '') {
                    $skipped++;
                    continue;
                }

                $existingTweet = RawTweet::query()
                    ->with('storyClusterItem')
                    ->where('tweet_id', $tweetId)
                    ->first();

                if ($existingTweet) {
                    $skipped++;

                    if ($existingTweet->storyClusterItem?->story_cluster_id) {
                        $clusterIds[] = $existingTweet->storyClusterItem->story_cluster_id;
                    }

                    continue;
                }

                $tweet = RawTweet::create([
                    'source_account_id' => $account->id,
                    'tweet_id' => $tweetId,
                    'tweet_url' => $tweetData['tweet_url'] ?? null,
                    'tweet_text' => $tweetData['text'] ?? '',
                    'raw_payload' => $tweetData,
                    'tweeted_at' => $this->parseDate($tweetData['tweeted_at'] ?? null),
                    'like_count' => (int) ($tweetData['like_count'] ?? 0),
                    'retweet_count' => (int) ($tweetData['retweet_count'] ?? 0),
                    'reply_count' => (int) ($tweetData['reply_count'] ?? 0),
                    'view_count' => (int) ($tweetData['view_count'] ?? 0),
                    'quote_count' => (int) ($tweetData['quote_count'] ?? 0),
                    'fetched_at' => now(),
                ]);

                $normalized = $this->normalize($tweet->tweet_text);
                $tokens = $this->tokens($normalized);

                $normalizedText = TweetNormalizedText::create([
                    'raw_tweet_id' => $tweet->id,
                    'normalized_text' => $normalized,
                    'normalized_hash' => hash('sha256', $normalized),
                    'language' => $tweetData['language'] ?? null,
                    'tokens' => $tokens,
                ]);

                [$duplicate, $matchedTweet, $similarity, $matchType] = $this->findDuplicate($tweet, $normalizedText);

                DuplicateCheck::create([
                    'raw_tweet_id' => $tweet->id,
                    'matched_tweet_id' => $matchedTweet?->id,
                    'match_type' => $matchType,
                    'similarity_score' => $similarity,
                    'is_duplicate' => $duplicate,
                    'checked_at' => now(),
                ]);

                $cluster = $this->attachToStory($tweet, $normalizedText, $matchedTweet, $similarity);
                $this->scoreStory($cluster);

                $tweet->update(['is_processed' => true]);
                $created++;
                $processed++;
                $clusters += $cluster->wasRecentlyCreated ? 1 : 0;
                $clusterIds[] = $cluster->id;
            }

            $latestTweetId = collect($tweets)->pluck('tweet_id')->filter()->map(fn ($id) => (string) $id)->sortDesc()->first();

            $account->update([
                'last_checked_at' => now(),
                'last_seen_tweet_id' => $latestTweetId ?: $account->last_seen_tweet_id,
            ]);
        });

        SystemLog::create([
            'level' => 'info',
            'module' => 'twscrape',
            'message' => "{$account->username} icin tweet toplama tamamlandi.",
            'context_json' => compact('created', 'skipped', 'clusters'),
        ]);

        return [
            'created' => $created,
            'skipped' => $skipped,
            'clusters' => $clusters,
            'processed' => $processed,
            'story_cluster_ids' => array_values(array_unique($clusterIds)),
            'last_tweet_id' => collect($tweets)->pluck('tweet_id')->filter()->map(fn ($id) => (string) $id)->sortDesc()->first(),
        ];
    }

    private function normalize(string $text): string
    {
        $text = Str::lower($text);
        $text = preg_replace('/https?:\/\/\S+/u', ' ', $text) ?: $text;
        $text = preg_replace('/[@#][\pL\pN_]+/u', ' ', $text) ?: $text;
        $text = preg_replace('/[^\pL\pN\s]/u', ' ', $text) ?: $text;
        $text = preg_replace('/\s+/u', ' ', $text) ?: $text;

        return trim($text);
    }

    private function tokens(string $normalized): array
    {
        if ($normalized === '') {
            return [];
        }

        return array_values(array_unique(array_filter(explode(' ', $normalized), fn ($token) => mb_strlen($token) > 2)));
    }

    private function findDuplicate(RawTweet $tweet, TweetNormalizedText $normalizedText): array
    {
        $threshold = (float) config('news_collection.similarity.threshold');
        $bestTweet = null;
        $bestScore = 0.0;

        $candidates = TweetNormalizedText::query()
            ->with('rawTweet')
            ->where('raw_tweet_id', '!=', $tweet->id)
            ->latest()
            ->limit(50)
            ->get();

        foreach ($candidates as $candidate) {
            if ($this->hasSharedUrl($tweet, $candidate->rawTweet)) {
                return [true, $candidate->rawTweet, 100.0, 'url'];
            }

            similar_text($normalizedText->normalized_text, $candidate->normalized_text, $percent);

            if ($percent > $bestScore) {
                $bestScore = $percent;
                $bestTweet = $candidate->rawTweet;
            }
        }

        return [$bestScore >= $threshold, $bestTweet, round($bestScore, 2), $bestScore >= $threshold ? 'text' : 'none'];
    }

    private function hasSharedUrl(RawTweet $tweet, RawTweet $candidate): bool
    {
        $tweetLinks = collect($tweet->raw_payload['links'] ?? [])->map(fn ($url) => $this->normalizeUrl((string) $url))->filter();
        $candidateLinks = collect($candidate->raw_payload['links'] ?? [])->map(fn ($url) => $this->normalizeUrl((string) $url))->filter();

        return $tweetLinks->intersect($candidateLinks)->isNotEmpty();
    }

    private function normalizeUrl(string $url): string
    {
        $parts = parse_url($url);

        if (! is_array($parts) || empty($parts['host'])) {
            return '';
        }

        $host = preg_replace('/^www\./', '', strtolower($parts['host']));
        $path = rtrim($parts['path'] ?? '', '/');

        return $host.$path;
    }

    private function attachToStory(RawTweet $tweet, TweetNormalizedText $normalizedText, ?RawTweet $matchedTweet, float $similarity): StoryCluster
    {
        $matchedCluster = $matchedTweet?->storyClusterItem?->storyCluster;
        $cluster = $matchedCluster ?: StoryCluster::create([
            'cluster_hash' => hash('sha256', $normalizedText->normalized_hash.$tweet->source_account_id),
            'title' => Str::limit($tweet->tweet_text, 90, ''),
            'summary' => Str::limit($tweet->tweet_text, 240),
            'category_id' => $tweet->sourceAccount->category_id,
            'main_source_account_id' => $tweet->source_account_id,
            'first_seen_at' => $tweet->tweeted_at ?: now(),
            'last_updated_at' => now(),
            'status' => 'open',
        ]);

        StoryClusterItem::create([
            'story_cluster_id' => $cluster->id,
            'raw_tweet_id' => $tweet->id,
            'relation_type' => $matchedCluster ? ($similarity >= 90 ? 'same_news' : 'alternate_source') : 'same_news',
            'item_score' => $this->tweetScore($tweet),
        ]);

        $cluster->update([
            'last_updated_at' => now(),
            'summary' => $cluster->summary ?: Str::limit($tweet->tweet_text, 240),
        ]);

        return $cluster;
    }

    private function scoreStory(StoryCluster $cluster): void
    {
        $cluster->loadMissing('items.rawTweet.sourceAccount');

        $sourceTrust = (float) $cluster->items->avg(fn (StoryClusterItem $item) => $item->rawTweet->sourceAccount->trust_score);
        $engagement = (float) $cluster->items->sum(fn (StoryClusterItem $item) => $this->tweetScore($item->rawTweet));
        $recency = $cluster->last_updated_at?->greaterThan(now()->subMinutes(30)) ? 20 : 8;
        $velocity = min(20, $cluster->items->count() * 5);
        $similarityBonus = min(15, max(0, $cluster->items->count() - 1) * 5);
        $final = round(($sourceTrust * 0.4) + ($engagement * 0.35) + $recency + $velocity + $similarityBonus, 2);

        StoryScore::create([
            'story_cluster_id' => $cluster->id,
            'source_trust_score' => $sourceTrust,
            'engagement_score' => $engagement,
            'recency_score' => $recency,
            'velocity_score' => $velocity,
            'duplicate_penalty' => 0,
            'similarity_bonus' => $similarityBonus,
            'final_score' => $final,
            'scored_at' => now(),
        ]);

        $cluster->update(['story_score' => $final]);
    }

    private function tweetScore(RawTweet $tweet): float
    {
        return round(
            ($tweet->like_count * 0.08)
            + ($tweet->retweet_count * 0.35)
            + ($tweet->reply_count * 0.2)
            + ($tweet->quote_count * 0.3)
            + min(25, $tweet->view_count / 1000),
            2
        );
    }

    private function parseDate(?string $value): ?Carbon
    {
        if (! $value) {
            return null;
        }

        return Carbon::parse($value);
    }
}
