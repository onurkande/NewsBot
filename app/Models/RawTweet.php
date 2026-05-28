<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RawTweet extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_account_id',
        'tweet_id',
        'tweet_url',
        'tweet_text',
        'raw_payload',
        'tweeted_at',
        'like_count',
        'retweet_count',
        'reply_count',
        'view_count',
        'quote_count',
        'fetched_at',
        'is_processed',
    ];

    protected function casts(): array
    {
        return [
            'raw_payload' => 'array',
            'tweeted_at' => 'datetime',
            'fetched_at' => 'datetime',
            'is_processed' => 'boolean',
            'like_count' => 'integer',
            'retweet_count' => 'integer',
            'reply_count' => 'integer',
            'view_count' => 'integer',
            'quote_count' => 'integer',
        ];
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(SourceAccount::class);
    }

    public function normalizedText(): HasOne
    {
        return $this->hasOne(TweetNormalizedText::class);
    }

    public function storyClusterItem(): HasOne
    {
        return $this->hasOne(StoryClusterItem::class);
    }
}
