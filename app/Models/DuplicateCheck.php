<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuplicateCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'raw_tweet_id',
        'matched_tweet_id',
        'match_type',
        'similarity_score',
        'is_duplicate',
        'checked_at',
    ];

    protected function casts(): array
    {
        return [
            'similarity_score' => 'decimal:2',
            'is_duplicate' => 'boolean',
            'checked_at' => 'datetime',
        ];
    }

    public function rawTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class);
    }

    public function matchedTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class, 'matched_tweet_id');
    }
}
