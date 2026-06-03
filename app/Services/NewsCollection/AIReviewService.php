<?php

namespace App\Services\NewsCollection;

use App\Models\AiGeneration;
use Illuminate\Support\Facades\DB;

class AIReviewService
{
    public function __construct(
        private AIGenerationLogService $logService
    ) {}

    public function approve(AiGeneration $generation): AiGeneration
    {
        if ($generation->status !== 'draft') {
            throw new \RuntimeException("Sadece 'draft' durumundaki uretimler onaylanabilir. Mevcut: {$generation->status}");
        }

        return DB::transaction(function () use ($generation) {
            $generation->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);

            $this->logService->logInfo($generation, 'Uretim onaylandi.', [
                'generation_id' => $generation->id,
            ]);

            return $generation->refresh();
        });
    }

    public function reject(AiGeneration $generation): AiGeneration
    {
        if ($generation->status !== 'draft' && $generation->status !== 'approved') {
            throw new \RuntimeException("Sadece 'draft' veya 'approved' durumundaki uretimler reddedilebilir. Mevcut: {$generation->status}");
        }

        return DB::transaction(function () use ($generation) {
            $generation->update([
                'status' => 'rejected',
                'approved_at' => null,
            ]);

            $this->logService->logInfo($generation, 'Uretim reddedildi.', [
                'generation_id' => $generation->id,
            ]);

            return $generation->refresh();
        });
    }

    public function publish(AiGeneration $generation): AiGeneration
    {
        if ($generation->status !== 'approved') {
            throw new \RuntimeException("Sadece onaylanmis uretimler yayinlanabilir. Mevcut: {$generation->status}");
        }

        return DB::transaction(function () use ($generation) {
            $generation->update(['status' => 'published']);

            $this->logService->logInfo($generation, 'Uretim yayina alindi.', [
                'generation_id' => $generation->id,
            ]);

            return $generation->refresh();
        });
    }
}
