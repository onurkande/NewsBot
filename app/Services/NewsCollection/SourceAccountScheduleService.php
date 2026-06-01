<?php

namespace App\Services\NewsCollection;

use App\Models\SourceAccount;

class SourceAccountScheduleService
{
    public function scheduleNext(SourceAccount $account): SourceAccount
    {
        $min = max(1, (int) ($account->min_check_interval_minutes ?: $account->check_interval_minutes ?: 15));
        $max = max($min, (int) ($account->max_check_interval_minutes ?: $min));
        $nextInterval = random_int($min, $max);

        $account->update([
            'next_check_interval_minutes' => $nextInterval,
            'next_check_at' => now()->addMinutes($nextInterval),
        ]);

        return $account->refresh();
    }
}
