<?php

namespace App\Services\NewsCollection;

class PromptResolverService
{
    private const ALLOWED_PLACEHOLDERS = [
        'tweet_content',
        'tweet_count',
        'sources',
        'total_score',
        'first_tweet',
    ];

    public static function validatePlaceholders(string $promptText): array
    {
        preg_match_all('/\{(\w+)\}/', $promptText, $matches);
        $invalid = [];

        foreach ($matches[1] as $placeholder) {
            if (! in_array($placeholder, self::ALLOWED_PLACEHOLDERS, true)) {
                $invalid[] = $placeholder;
            }
        }

        return $invalid;
    }

    public function resolve(string $promptText, \Illuminate\Support\Collection $tweets): string
    {
        $variables = $this->extractVariables($tweets);

        return $this->replaceVariables($promptText, $variables);
    }

    public function resolveForTweet(string $promptText, \App\Models\RawTweet $tweet): string
    {
        $variables = $this->extractVariablesForTweet($tweet);

        return $this->replaceVariables($promptText, $variables);
    }

    public function buildFullPrompt(string $resolvedPrompt, \Illuminate\Support\Collection $tweets): string
    {
        $tweetTexts = $tweets->map(function (array $tweet, int $index) {
            $num = $index + 1;
            $username = $tweet['username'] ?? 'bilinmiyor';
            $text = $tweet['tweet_text'] ?? '';

            return "Tweet {$num} (@{$username}):\n{$text}\n";
        })->implode("\n");

        if (str_contains($resolvedPrompt, '{tweet_content}')) {
            return $resolvedPrompt;
        }

        return "{$resolvedPrompt}\n\n--- SECILEN TWEETLER ---\n\n{$tweetTexts}";
    }

    private function extractVariables(\Illuminate\Support\Collection $tweets): array
    {
        $usernames = $tweets->pluck('username')->filter()->unique()->values()->implode(', ');
        $tweetCount = $tweets->count();
        $totalScore = round((float) $tweets->avg('final_score'), 2);
        $firstTweet = $tweets->first()['tweet_text'] ?? '';

        $tweetContent = $tweets->map(function (array $tweet, int $index) {
            $num = $index + 1;
            $username = $tweet['username'] ?? 'bilinmiyor';
            $text = $tweet['tweet_text'] ?? '';

            return "Tweet {$num} (@{$username}):\n{$text}";
        })->implode("\n\n");

        return [
            '{tweet_content}' => $tweetContent,
            '{tweet_count}' => (string) $tweetCount,
            '{sources}' => $usernames,
            '{total_score}' => (string) $totalScore,
            '{first_tweet}' => $firstTweet,
        ];
    }

    private function extractVariablesForTweet(\App\Models\RawTweet $tweet): array
    {
        $username = $tweet->sourceAccount?->username ?? 'bilinmiyor';
        $finalScore = $tweet->poolBatchItems->first()?->final_score ?? 0;

        return [
            '{tweet_content}' => $tweet->tweet_text,
            '{tweet_count}' => '1',
            '{sources}' => $username,
            '{total_score}' => (string) $finalScore,
            '{first_tweet}' => $tweet->tweet_text,
        ];
    }

    private function replaceVariables(string $text, array $variables): string
    {
        return str_replace(array_keys($variables), array_values($variables), $text);
    }
}
