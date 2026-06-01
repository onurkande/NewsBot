<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoolBatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pool_batch_id',
        'raw_tweet_id',
        'priority_score',
        'engagement_score',
        'final_score',
        'is_selected',
        'rank',
    ];

    protected function casts(): array
    {
        return [
            'priority_score' => 'decimal:2',
            'engagement_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'is_selected' => 'boolean',
            'rank' => 'integer',
        ];
    }

    public function poolBatch(): BelongsTo
    {
        return $this->belongsTo(PoolBatch::class);
    }

    public function rawTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class);
    }
}
