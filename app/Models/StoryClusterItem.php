<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryClusterItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_cluster_id',
        'raw_tweet_id',
        'relation_type',
        'item_score',
    ];

    protected function casts(): array
    {
        return [
            'item_score' => 'decimal:2',
        ];
    }

    public function storyCluster(): BelongsTo
    {
        return $this->belongsTo(StoryCluster::class);
    }

    public function rawTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class);
    }
}
