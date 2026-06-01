<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClusterScanHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_cluster_id',
        'source_account_id',
        'scanned_at',
        'previous_scan_elapsed_minutes',
        'fetched_tweet_count',
        'new_tweet_count',
        'processed_tweet_count',
        'skipped_tweet_count',
        'last_tweet_id',
        'duration_ms',
        'status',
        'has_error',
        'error_message',
        'next_check_interval_minutes',
        'next_check_at',
    ];

    protected function casts(): array
    {
        return [
            'scanned_at' => 'datetime',
            'next_check_at' => 'datetime',
            'previous_scan_elapsed_minutes' => 'integer',
            'fetched_tweet_count' => 'integer',
            'new_tweet_count' => 'integer',
            'processed_tweet_count' => 'integer',
            'skipped_tweet_count' => 'integer',
            'duration_ms' => 'integer',
            'has_error' => 'boolean',
            'next_check_interval_minutes' => 'integer',
        ];
    }

    public function storyCluster(): BelongsTo
    {
        return $this->belongsTo(StoryCluster::class);
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(SourceAccount::class);
    }
}
