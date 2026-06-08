<?php

namespace App\Services\NewsCollection;

use App\Models\AiGeneration;
use App\Models\AiQueue;
use App\Models\AiSetting;
use App\Models\PoolBatchItem;
use App\Models\Prompt;
use App\Models\RawTweet;
use App\Services\Admin\AiQueueService;
use Illuminate\Support\Facades\DB;

class AIGenerationService
{
    public function __construct(
        private AiQueueService $queueService,
        private AIGenerationLogService $logService,
        private PromptResolverService $promptResolver,
        private AIProviderFactory $providerFactory,
        private AIReviewService $reviewService,
    ) {}

    public function processQueue(AiQueue $queue): void
    {
        $settings = AiSetting::singleton();

        if (! $settings->is_active) {
            $this->queueService->markFailed($queue, 'AI uretimi pasif durumda.');
            throw new \RuntimeException('AI uretimi pasif durumda.');
        }

        if ($queue->status !== 'pending') {
            throw new \RuntimeException("Kuyruk durumu 'pending' olmali, mevcut: {$queue->status}");
        }

        $this->queueService->markProcessing($queue);
        $this->logService->logQueue($queue, 'info', 'AI uretimi baslatildi.', [
            'provider' => $settings->provider,
        ]);

        $selectedTweets = $this->getSelectedTweets($queue);

        if ($selectedTweets->isEmpty()) {
            $this->queueService->markCompleted($queue);
            $this->logService->logQueue($queue, 'warning', 'Batch\'te secilmis tweet yok, uretim atlandi.');

            return;
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($selectedTweets as $tweet) {
            try {
                $this->generateForTweet($tweet, $queue, $settings);
                $successCount++;
            } catch (\Throwable $e) {
                $failCount++;
                $this->logService->logQueue($queue, 'error', "Tweet ID {$tweet->id} icin AI uretimi basarisiz oldu.", [
                    'raw_tweet_id' => $tweet->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($failCount > 0 && $successCount === 0) {
            $this->queueService->markFailed($queue, "Tum tweetler icin AI uretimi basarisiz oldu. ({$failCount} hata)");
            throw new \RuntimeException("Tum tweetler icin AI uretimi basarisiz oldu.");
        }

        $this->queueService->markCompleted($queue);
        $this->logService->logQueue($queue, 'info', 'AI uretimi tamamlandi.', [
            'success_count' => $successCount,
            'fail_count' => $failCount,
        ]);
    }

    private function getSelectedTweets(AiQueue $queue): \Illuminate\Support\Collection
    {
        return PoolBatchItem::query()
            ->with('rawTweet.sourceAccount.category')
            ->where('pool_batch_id', $queue->pool_batch_id)
            ->where('is_selected', true)
            ->orderBy('final_score', 'desc')
            ->get()
            ->pluck('rawTweet')
            ->filter();
    }

    private function generateForTweet(RawTweet $tweet, AiQueue $queue, AiSetting $settings): AiGeneration
    {
        $prompt = $this->resolvePrompt($settings, $tweet);
        $provider = $this->providerFactory->make($settings->provider);
        $model = $this->getModelForProvider($settings);

        $resolvedPrompt = $this->promptResolver->resolveForTweet($prompt->prompt_text, $tweet);
        $fullPrompt = $this->buildFullPrompt($resolvedPrompt, $tweet);

        $startTime = microtime(true);

        $response = $provider->generate(
            $fullPrompt,
            $model,
            $settings->timeout,
            $settings->retry_count
        );

        $duration = (int) round((microtime(true) - $startTime) * 1000);

        return $this->saveGeneration($queue, $settings, $tweet, $prompt, $response, $duration, $fullPrompt);
    }

    private function resolvePrompt(AiSetting $settings, RawTweet $tweet): Prompt
    {
        $categoryId = $tweet->sourceAccount?->category_id;

        if ($categoryId) {
            $activePrompt = Prompt::query()
                ->where('source_category_id', $categoryId)
                ->where('is_active', true)
                ->first();
        }

        if (empty($activePrompt) && $settings->active_prompt_id) {
            $activePrompt = Prompt::find($settings->active_prompt_id);
        }

        if (empty($activePrompt)) {
            $activePrompt = Prompt::query()
                ->where('is_active', true)
                ->whereNull('source_category_id')
                ->first();
        }

        if (empty($activePrompt)) {
            $activePrompt = new Prompt([
                'prompt_text' => $this->defaultPrompt(),
                'version' => 1,
            ]);
        }

        return $activePrompt;
    }

    private function getModelForProvider(AiSetting $settings): ?string
    {
        return match ($settings->provider) {
            'opencode' => $settings->opencode_model ?: 'deepseek-v4-flash',
            default => $settings->model_name,
        };
    }

    private function saveGeneration(
        AiQueue $queue,
        AiSetting $settings,
        RawTweet $tweet,
        Prompt $prompt,
        mixed $response,
        int $duration,
        string $fullPrompt
    ): AiGeneration {
        $generation = DB::transaction(function () use ($queue, $settings, $tweet, $prompt, $response, $duration, $fullPrompt) {
            $generation = AiGeneration::create([
                'ai_queue_id' => $queue->id,
                'raw_tweet_id' => $tweet->id,
                'source_account_id' => $tweet->source_account_id,
                'category_id' => $tweet->sourceAccount?->category_id,
                'provider' => $settings->provider,
                'prompt_id' => $prompt->id,
                'model' => $response['model'] ?? $settings->opencode_model ?? $settings->model_name ?? $settings->provider,
                'prompt_version' => $prompt->version ?? 1,
                'title' => $response['title'] ?? null,
                'input' => [
                    'tweet_id' => $tweet->tweet_id,
                    'tweet_text' => $tweet->tweet_text,
                    'username' => $tweet->sourceAccount?->username,
                ],
                'prompt' => $prompt->prompt_text,
                'full_prompt' => $fullPrompt,
                'ai_response' => $response['raw'] ?? json_encode($response),
                'generated_news' => $response['content'] ?? $response['raw'] ?? null,
                'token_usage' => $response['token_usage'] ?? null,
                'duration' => $duration,
                'status' => 'draft',
                'generated_at' => now(),
            ]);

            $this->logService->logInfo($generation, 'AI uretimi basariyla tamamlandi.', [
                'provider' => $response['provider'] ?? $settings->provider,
                'model' => $response['model'] ?? $settings->model_name,
                'duration_ms' => $duration,
                'raw_tweet_id' => $tweet->id,
            ]);

            return $generation;
        });

        if ($settings->auto_approve) {
            try {
                $this->reviewService->approve($generation);
            } catch (\Throwable $e) {
                $this->logService->logWarning($generation, 'Otomatik onay basarisiz oldu.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if ($settings->auto_approve && $settings->auto_publish) {
            try {
                $this->reviewService->publish($generation);
            } catch (\Throwable $e) {
                $this->logService->logWarning($generation, 'Otomatik yayin planlama basarisiz oldu.', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $generation;
    }

    private function buildFullPrompt(string $resolvedPrompt, RawTweet $tweet): string
    {
        $username = $tweet->sourceAccount?->username ?? 'bilinmiyor';
        $tweetText = $tweet->tweet_text ?? '';

        $tweetBlock = "Kaynak: @{$username}\nTweet: {$tweetText}";

        if (str_contains($resolvedPrompt, '{tweet_content}')) {
            return $resolvedPrompt;
        }

        return "{$resolvedPrompt}\n\n--- TWEET ---\n\n{$tweetBlock}";
    }

    private function defaultPrompt(): string
    {
        return <<<'PROMPT'
Asagidaki tweeti kullanarak kapsamli ve objektif bir haber metni olustur.

{tweet_content}

Haber dilinde, tarafsiz ve bilgilendirici bir metin yaz.
Haber basligi ve govde metni olustur. Toplam 200-300 kelime arasinda olsun.

Cikti formati:
BASLIK: [Haber Basligi]
ICERIK: [Haber Metni]
PROMPT;
    }
}
