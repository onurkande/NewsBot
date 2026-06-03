<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_generation_id',
        'raw_tweet_id',
    ];

    public function generation(): BelongsTo
    {
        return $this->belongsTo(AiGeneration::class);
    }

    public function rawTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class);
    }
}
