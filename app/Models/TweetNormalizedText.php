<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TweetNormalizedText extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_tweet_id',
        'normalized_text',
        'normalized_hash',
        'language',
        'tokens',
    ];

    protected function casts(): array
    {
        return [
            'tokens' => 'array',
        ];
    }

    public function rawTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class);
    }
}
