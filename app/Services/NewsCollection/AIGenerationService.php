<?php

namespace App\Services\NewsCollection;

use App\Models\AiGeneration;
use App\Models\AiGenerationItem;
use App\Models\AiQueue;
use App\Models\AiSetting;
use App\Models\PoolBatchItem;
use App\Models\Prompt;
use App\Services\Admin\AiQueueService;
use Illuminate\Support\Facades\DB;

class AIGenerationService
{
    public function __construct(
        private AiQueueService $queueService,
        private AIGenerationLogService $logService,
        private PromptResolverService $promptResolver,
        private AIProviderFactory $providerFactory,
    ) {}

    public function processQueue(AiQueue $queue): AiGeneration
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

        $startTime = microtime(true);

        try {
            $selectedTweets = $this->getSelectedTweets($queue);
            $prompt = $this->resolvePrompt($settings, $selectedTweets);

            $provider = $this->providerFactory->make($settings->provider);

            $model = $this->getModelForProvider($settings);

            $response = $provider->generate(
                $prompt['full_prompt'],
                $model,
                $settings->timeout,
                $settings->retry_count
            );

            $duration = (int) round((microtime(true) - $startTime) * 1000);

            $generation = $this->saveGeneration($queue, $settings, $prompt, $response, $duration, $selectedTweets);

            $this->queueService->markCompleted($queue);
            $this->logService->logInfo($generation, 'AI uretimi basariyla tamamlandi.', [
                'provider' => $response['provider'] ?? $settings->provider,
                'model' => $response['model'] ?? $model,
                'duration_ms' => $duration,
                'tweet_count' => $selectedTweets->count(),
            ]);

            return $generation;

        } catch (\Throwable $e) {
            $this->queueService->markFailed($queue, $e->getMessage());
            $this->logService->logQueue($queue, 'error', 'AI uretimi basarisiz oldu.', [
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    private function getSelectedTweets(AiQueue $queue): \Illuminate\Support\Collection
    {
        return PoolBatchItem::query()
            ->with('rawTweet.sourceAccount.category')
            ->where('pool_batch_id', $queue->pool_batch_id)
            ->where('is_selected', true)
            ->orderBy('final_score', 'desc')
            ->get()
            ->map(fn (PoolBatchItem $item) => [
                'id' => $item->rawTweet->id,
                'tweet_id' => $item->rawTweet->tweet_id,
                'tweet_text' => $item->rawTweet->tweet_text,
                'username' => $item->rawTweet->sourceAccount?->username,
                'category_id' => $item->rawTweet->sourceAccount?->category_id,
                'final_score' => (float) $item->final_score,
            ]);
    }

    private function resolvePrompt(AiSetting $settings, \Illuminate\Support\Collection $tweets): array
    {
        $activePrompt = null;

        $firstTweetCategoryId = $tweets->first()['category_id'] ?? null;

        if ($firstTweetCategoryId) {
            $activePrompt = Prompt::query()
                ->where('source_category_id', $firstTweetCategoryId)
                ->where('is_active', true)
                ->first();
        }

        if (! $activePrompt && $settings->active_prompt_id) {
            $activePrompt = Prompt::find($settings->active_prompt_id);
        }

        if (! $activePrompt) {
            $activePrompt = Prompt::query()
                ->where('is_active', true)
                ->whereNull('source_category_id')
                ->first();
        }

        if (! $activePrompt) {
            $defaultText = $this->defaultPrompt();
        } else {
            $defaultText = $activePrompt->prompt_text;
        }

        $resolvedPrompt = $this->promptResolver->resolve($defaultText, $tweets);
        $fullPrompt = $this->promptResolver->buildFullPrompt($resolvedPrompt, $tweets);

        return [
            'prompt_id' => $activePrompt?->id,
            'prompt_version' => $activePrompt?->version ?? 1,
            'prompt' => $defaultText,
            'full_prompt' => $fullPrompt,
            'resolved_prompt' => $resolvedPrompt,
        ];
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
        array $promptData,
        mixed $response,
        int $duration,
        \Illuminate\Support\Collection $tweets
    ): AiGeneration {
        return DB::transaction(function () use ($queue, $settings, $promptData, $response, $duration, $tweets) {
            $generation = AiGeneration::create([
                'ai_queue_id' => $queue->id,
                'prompt_id' => $promptData['prompt_id'],
                'model' => $response['model'] ?? $settings->opencode_model ?? $settings->model_name ?? $settings->provider,
                'prompt_version' => $promptData['prompt_version'],
                'title' => $response['title'] ?? null,
                'input' => $tweets->toArray(),
                'prompt' => $promptData['prompt'],
                'full_prompt' => $promptData['full_prompt'],
                'ai_response' => $response['raw'] ?? json_encode($response),
                'generated_news' => $response['content'] ?? $response['raw'] ?? null,
                'token_usage' => $response['token_usage'] ?? null,
                'duration' => $duration,
                'status' => 'draft',
                'generated_at' => now(),
            ]);

            foreach ($tweets as $tweet) {
                AiGenerationItem::create([
                    'ai_generation_id' => $generation->id,
                    'raw_tweet_id' => $tweet['id'],
                ]);
            }

            return $generation;
        });
    }

    private function defaultPrompt(): string
    {
        return <<<'PROMPT'
Asagidaki tweetler bir haber olusturmak icin secilmistir.
Bu tweetleri kullanarak kapsamli ve objektif bir haber metni olustur.

{tweet_content}

Tum tweetleri dikkate al. Haber dilinde, tarafsiz ve bilgilendirici bir metin yaz.
Haber basligi ve govde metni olustur. Toplam 250-400 kelime arasinda olsun.

Cikti formati:
BASLIK: [Haber Basligi]
ICERIK: [Haber Metni]
PROMPT;
    }
}
