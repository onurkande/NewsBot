<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoolBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'batch_no',
        'tweet_window_minutes',
        'candidate_count',
        'selected_count',
        'wait_duration_minutes',
        'next_run_at',
        'started_at',
        'completed_at',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'tweet_window_minutes' => 'integer',
            'candidate_count' => 'integer',
            'selected_count' => 'integer',
            'wait_duration_minutes' => 'integer',
            'next_run_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => 'string',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(PoolBatchItem::class)->orderByDesc('final_score');
    }

    public function aiQueue(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(\App\Models\AiQueue::class);
    }
}
