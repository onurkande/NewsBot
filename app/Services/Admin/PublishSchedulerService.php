<?php

namespace App\Services\Admin;

use App\Models\AiGeneration;
use App\Models\PublishAccount;
use App\Models\PublishLog;
use App\Models\PublishQueue;
use App\Models\PublishSettings;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

class PublishSchedulerService
{

    /**
     * Her dakika calisir. approved kayitlari kontrol eder:
     * - expiration suresi dolmus mu? → expired yap
     * - limitler uygun mu? → publish_queue olustur
     */
    public function processApproved(): void
    {
        $settings = PublishSettings::singleton();
        $account = $this->resolveDefaultAccount();

        if (! $account) {
            $this->log('warning', 'Aktif yayin hesabi yok, processApproved atlaniyor.');
            return;
        }

        $approved = AiGeneration::query()
            ->where('status', 'approved')
            ->whereDoesntHave('publishQueues', function ($q) {
                $q->whereIn('status', ['pending', 'processing']);
            })
            ->whereNotNull('approved_at')
            ->orderBy('approved_at')
            ->get();

        foreach ($approved as $generation) {
            if ($settings->publish_expiration_hours > 0 && $generation->approved_at) {
                $expiresAt = $generation->approved_at->copy()->addHours($settings->publish_expiration_hours);
                if (now()->isAfter($expiresAt)) {
                    $this->markExpired($generation);
                    continue;
                }
            }

            if (! $this->canPublishToday($settings, $account)) {
                $this->log('info', 'Gunluk limite takildi, bekletiliyor.', [
                    'ai_generation_id' => $generation->id,
                    'daily_limit' => $this->getDailyLimit($settings, $account),
                ]);
                continue;
            }

            if (! $this->canPublishThisHour($settings, $account)) {
                $this->log('info', 'Saatlik limite takildi, bekletiliyor.', [
                    'ai_generation_id' => $generation->id,
                ]);
                continue;
            }

            if (! $this->isWithinPublishHours($settings)) {
                $this->log('info', 'Gece moduna takildi, bekletiliyor.', [
                    'ai_generation_id' => $generation->id,
                ]);
                continue;
            }

            if (! $this->minGapPassed($settings, $account)) {
                $this->log('info', 'Minimum gap surei dolmadi, bekletiliyor.', [
                    'ai_generation_id' => $generation->id,
                ]);
                continue;
            }

            try {
                $this->schedule($generation, $account);
            } catch (\RuntimeException $e) {
                $this->log('warning', 'Otomatik planlama basarisiz.', [
                    'ai_generation_id' => $generation->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    public function markExpired(AiGeneration $generation): void
    {
        DB::transaction(function () use ($generation) {
            $generation->update(['status' => 'expired']);

            $this->log('info', 'Icerik expiration nedeniyle expired oldu.', [
                'ai_generation_id' => $generation->id,
                'approved_at' => $generation->approved_at?->toDateTimeString(),
                'expiration_hours' => PublishSettings::singleton()->publish_expiration_hours,
            ]);
        });
    }

    /**
     * Bir AI Generation icin yayin plani olusturur.
     * Kurallar kontrol edilir, uygun zamanda publish_queue kaydi olusturulur.
     */
    public function schedule(AiGeneration $generation, ?PublishAccount $account = null): PublishQueue
    {
        $settings = PublishSettings::singleton();

        if (! $settings->auto_publish_enabled && ! $this->isManualTrigger()) {
            throw new \RuntimeException('Otomatik yayin pasif durumda.');
        }

        $account = $account ?: $this->resolveDefaultAccount();
        if (! $account) {
            throw new \RuntimeException('Aktif yayin hesabi bulunamadi.');
        }

        if ($generation->status !== 'approved' && $generation->status !== 'publish_failed') {
            throw new \RuntimeException("Sadece 'approved' veya 'publish_failed' durumundaki uretimler planlanabilir. Mevcut: {$generation->status}");
        }

        $existing = PublishQueue::query()
            ->where('ai_generation_id', $generation->id)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        if ($existing) {
            throw new \RuntimeException('Bu uretim zaten yayin kuyrugunda.');
        }

        $scheduledAt = $this->resolveScheduledAt($settings, $account);

        return DB::transaction(function () use ($generation, $account, $scheduledAt, $settings) {
            $queue = PublishQueue::create([
                'ai_generation_id' => $generation->id,
                'publish_account_id' => $account->id,
                'status' => 'pending',
                'scheduled_at' => $scheduledAt,
            ]);

            $this->log('info', 'Yayin planlandi.', [
                'ai_generation_id' => $generation->id,
                'publish_queue_id' => $queue->id,
                'publish_account_id' => $account->id,
                'scheduled_at' => $scheduledAt->toDateTimeString(),
                'daily_limit' => $this->getDailyLimit($settings, $account),
                'hourly_limit' => $settings->hourly_post_limit,
            ]);

            return $queue;
        });
    }

    /**
     * Pending durumda ve zamani gelmis kuyruk kayitlarini bulup PublishPostJob dispatch eder.
     */
    public function dispatchPending(): void
    {
        $settings = PublishSettings::singleton();

        $pending = PublishQueue::query()
            ->with(['publishAccount', 'aiGeneration.rawTweet'])
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->orderBy('scheduled_at')
            ->get();

        foreach ($pending as $queue) {
            $account = $queue->publishAccount;
            if (! $account || ! $account->is_active) {
                $this->log('warning', 'Hesap pasif, yayin atlaniyor.', [
                    'publish_queue_id' => $queue->id,
                ]);
                continue;
            }

            if (! $this->isWithinPublishHours($settings)) {
                $this->log('info', 'Gece modu aktif, yayin ertelendi.', [
                    'publish_queue_id' => $queue->id,
                ]);
                continue;
            }

            if (! $this->canPublishThisHour($settings, $account)) {
                $this->log('info', 'Saatlik limite takildi, yayin ertelendi.', [
                    'publish_queue_id' => $queue->id,
                    'hourly_limit' => $settings->hourly_post_limit,
                ]);
                continue;
            }

            if (! $this->canPublishToday($settings, $account)) {
                $this->log('info', 'Gunluk limite takildi, yayin ertelendi.', [
                    'publish_queue_id' => $queue->id,
                    'daily_limit' => $this->getDailyLimit($settings, $account),
                ]);
                continue;
            }

            if (! $this->minGapPassed($settings, $account)) {
                $this->log('info', 'Minimum gap suresi dolmadi, yayin ertelendi.', [
                    'publish_queue_id' => $queue->id,
                    'min_gap_minutes' => $settings->min_gap_minutes,
                ]);
                continue;
            }

            $this->log('info', 'PublishPostJob dispatch edildi.', [
                'publish_queue_id' => $queue->id,
                'ai_generation_id' => $queue->ai_generation_id,
            ]);

            \App\Jobs\PublishPostJob::dispatch($queue->id);
        }
    }

    /**
     * Bir sonraki uygun zaman dilimini hesaplar.
     */
    public function resolveScheduledAt(PublishSettings $settings, PublishAccount $account): \Carbon\Carbon
    {
        $base = now();

        // Random delay
        $delay = random_int($settings->min_delay_minutes, $settings->max_delay_minutes);
        $base = $base->addMinutes($delay);

        // Min gap kontrolu
        $lastPublished = $this->getLastPublishedAt($account);
        if ($lastPublished) {
            $gapEnd = $lastPublished->copy()->addMinutes($settings->min_gap_minutes);
            if ($base->isBefore($gapEnd)) {
                $base = $gapEnd;
            }
        }

        // Gece modu kontrolu
        $currentHour = (int) $base->format('G');
        if ($currentHour < $settings->publish_start_hour) {
            $base = $base->copy()->setTime($settings->publish_start_hour, 0, 0);
        } elseif ($currentHour >= $settings->publish_end_hour) {
            $base = $base->copy()->addDay()->setTime($settings->publish_start_hour, 0, 0);
        }

        return $base;
    }

    public function isWithinPublishHours(PublishSettings $settings): bool
    {
        $hour = (int) now()->format('G');
        return $hour >= $settings->publish_start_hour && $hour < $settings->publish_end_hour;
    }

    public function canPublishToday(PublishSettings $settings, PublishAccount $account): bool
    {
        $limit = $this->getDailyLimit($settings, $account);

        $todayCount = PublishLog::query()
            ->where('publish_account_id', $account->id)
            ->where('status', 'published')
            ->whereDate('published_at', today())
            ->count();

        return $todayCount < $limit;
    }

    public function canPublishThisHour(PublishSettings $settings, PublishAccount $account): bool
    {
        $hourCount = PublishLog::query()
            ->where('publish_account_id', $account->id)
            ->where('status', 'published')
            ->where('published_at', '>=', now()->startOfHour())
            ->count();

        return $hourCount < $settings->hourly_post_limit;
    }

    public function minGapPassed(PublishSettings $settings, PublishAccount $account): bool
    {
        $lastPublished = $this->getLastPublishedAt($account);
        if (! $lastPublished) {
            return true;
        }

        return now()->diffInMinutes($lastPublished, false) >= $settings->min_gap_minutes;
    }

    public function getDailyLimit(PublishSettings $settings, PublishAccount $account): int
    {
        if ($settings->warmup_mode_enabled && $account->account_created_at) {
            $days = $account->account_created_at->diffInDays(now());
            if ($days <= 30) {
                return $settings->warmup_0_30_daily_limit;
            } elseif ($days <= 60) {
                return $settings->warmup_30_60_daily_limit;
            } else {
                return $settings->warmup_60_plus_daily_limit;
            }
        }

        return $settings->daily_post_limit;
    }

    public function getLastPublishedAt(PublishAccount $account): ?\Carbon\Carbon
    {
        $last = PublishLog::query()
            ->where('publish_account_id', $account->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->value('published_at');

        return $last;
    }

    public function resolveDefaultAccount(): ?PublishAccount
    {
        return PublishAccount::query()
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }

    private function isManualTrigger(): bool
    {
        // Admin panelden calistirildigini anlamak icin.
        // Request varsa ve admin route'undaysak manuel kabul edilir.
        return app()->runningInConsole() === false;
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'publish_scheduler',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
