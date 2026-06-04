<?php

namespace App\Services\NewsCollection;

use App\Models\RawTweet;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TweetMediaService
{
    private const DISK = 'local';
    private const BASE_PATH = 'media/tweets';

    /**
     * Python payload'tan medya bilgilerini cikarir ve dondurur.
     */
    public function extractMediaFromPayload(array $tweetData): array
    {
        $mediaUrls = [];
        $types = [];

        if (! empty($tweetData['photo_urls'])) {
            foreach ($tweetData['photo_urls'] as $url) {
                $mediaUrls[] = $url;
                $types[] = 'photo';
            }
        }

        if (! empty($tweetData['video_urls'])) {
            foreach ($tweetData['video_urls'] as $url) {
                $mediaUrls[] = $url;
                $types[] = 'video';
            }
        }

        if (! empty($tweetData['animated_gif_urls'])) {
            foreach ($tweetData['animated_gif_urls'] as $url) {
                $mediaUrls[] = $url;
                $types[] = 'animated_gif';
            }
        }

        $count = count($mediaUrls);
        $uniqueTypes = array_values(array_unique($types));

        $mediaType = match (true) {
            $count === 0 => null,
            count($uniqueTypes) === 1 => $uniqueTypes[0],
            default => 'mixed',
        };

        return [
            'media_urls' => $mediaUrls,
            'media_count' => $count,
            'media_type' => $mediaType,
        ];
    }

    /**
     * Tweet'in medya dosyalarini indirir ve depolar.
     */
    public function downloadForTweet(RawTweet $tweet): void
    {
        $urls = $tweet->media_urls ?? [];

        if (empty($urls)) {
            $this->log('info', 'Medya indirme atlandi: URL yok.', [
                'raw_tweet_id' => $tweet->id,
            ]);

            return;
        }

        if (! empty($tweet->media_paths)) {
            $this->log('media_exists', 'Medya zaten indirilmis.', [
                'raw_tweet_id' => $tweet->id,
            ]);

            return;
        }

        $paths = [];
        $errors = [];

        foreach ($urls as $index => $url) {
            try {
                $path = $this->downloadFile($url, $tweet->id, $index);

                if ($path) {
                    $paths[] = $path;
                } else {
                    $errors[] = "URL indirilemedi: {$url}";
                }
            } catch (\Throwable $e) {
                $errors[] = "Hata ({$url}): " . $e->getMessage();
            }
        }

        $tweet->update([
            'media_paths' => $paths,
            'media_downloaded_at' => now(),
        ]);

        if (! empty($errors)) {
            $this->log('media_download_failed', 'Bazi medyalar indirilemedi.', [
                'raw_tweet_id' => $tweet->id,
                'errors' => $errors,
                'downloaded_count' => count($paths),
            ]);
        } else {
            $this->log('media_downloaded', 'Tum medyalar basariyla indirildi.', [
                'raw_tweet_id' => $tweet->id,
                'downloaded_count' => count($paths),
            ]);
        }
    }

    /**
     * Tweet'in medya dosyalarini fiziksel olarak siler ve kayitlari temizler.
     */
    public function deleteMedia(RawTweet $tweet): void
    {
        $paths = $tweet->media_paths ?? [];

        if (empty($paths)) {
            return;
        }

        $disk = Storage::disk(self::DISK);
        $deleted = 0;

        foreach ($paths as $path) {
            if ($disk->exists($path)) {
                $disk->delete($path);
                $deleted++;
            }
        }

        $tweet->update([
            'media_paths' => null,
            'media_downloaded_at' => null,
        ]);

        $this->log('media_deleted', 'Tweet medyalari temizlendi.', [
            'raw_tweet_id' => $tweet->id,
            'deleted_count' => $deleted,
        ]);
    }

    /**
     * Retention kurallarina gore eskimiş medya dosyalarini temizler.
     */
    public function cleanupExpired(): void
    {
        $settings = \App\Models\PoolSetting::singleton();

        if (! $settings->media_download_enabled) {
            $this->log('info', 'Medya temizlik isi atlandi: media_download_enabled kapali.');

            return;
        }

        $publishedThreshold = now()->subHours($settings->published_media_retention_hours);
        $unpublishedThreshold = now()->subHours($settings->unpublished_media_retention_hours);

        // Yayinlanmis tweetler (published_posts uzerinden kontrol edilecek)
        // Simdilik: ai_generations status = published varsa published kabul edilir.
        $publishedTweetIds = \App\Models\AiGeneration::query()
            ->where('status', 'published')
            ->whereNotNull('approved_at')
            ->where('approved_at', '<=', $publishedThreshold)
            ->pluck('raw_tweet_id')
            ->unique()
            ->values();

        // Yayinlanmamis tweetler: approved veya draft, ama published degil
        $unpublishedTweetIds = \App\Models\AiGeneration::query()
            ->whereIn('status', ['draft', 'approved', 'rejected'])
            ->whereNotNull('created_at')
            ->where('created_at', '<=', $unpublishedThreshold)
            ->pluck('raw_tweet_id')
            ->unique()
            ->values();

        $totalDeleted = 0;

        if ($publishedTweetIds->isNotEmpty()) {
            $tweets = RawTweet::query()
                ->whereIn('id', $publishedTweetIds)
                ->whereNotNull('media_paths')
                ->get();

            foreach ($tweets as $tweet) {
                $this->deleteMedia($tweet);
                $totalDeleted++;
            }
        }

        if ($unpublishedTweetIds->isNotEmpty()) {
            $tweets = RawTweet::query()
                ->whereIn('id', $unpublishedTweetIds)
                ->whereNotNull('media_paths')
                ->get();

            foreach ($tweets as $tweet) {
                $this->deleteMedia($tweet);
                $totalDeleted++;
            }
        }

        $this->log('cleanup_completed', 'Medya temizlik isi tamamlandi.', [
            'published_deleted' => $publishedTweetIds->count(),
            'unpublished_deleted' => $unpublishedTweetIds->count(),
            'total_deleted' => $totalDeleted,
        ]);
    }

    private function downloadFile(string $url, int $tweetId, int $index): ?string
    {
        $response = Http::timeout(30)
            ->withOptions([
                'verify' => false,
            ])
            ->get($url);

        if (! $response->successful()) {
            return null;
        }

        $extension = $this->guessExtension($url, $response->header('Content-Type'));
        $filename = sprintf('%s_%d.%s', md5($url), $index, $extension);
        $relativePath = self::BASE_PATH . "/{$tweetId}/{$filename}";

        Storage::disk(self::DISK)->put($relativePath, $response->body());

        return $relativePath;
    }

    private function guessExtension(string $url, ?string $contentType): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'video/mp4' => 'mp4',
            'video/webm' => 'webm',
            'application/octet-stream' => 'mp4',
        ];

        if ($contentType && isset($map[$contentType])) {
            return $map[$contentType];
        }

        $ext = pathinfo(parse_url($url, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);

        return $ext ?: 'bin';
    }

    private function log(string $event, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => str_contains($event, 'failed') ? 'error' : 'info',
            'module' => 'media_management',
            'message' => $message,
            'context_json' => array_merge(['event' => $event], $context),
        ]);
    }
}
