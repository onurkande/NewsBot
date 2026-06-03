<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiGeneration extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_queue_id',
        'raw_tweet_id',
        'source_account_id',
        'category_id',
        'provider',
        'prompt_id',
        'model',
        'prompt_version',
        'title',
        'input',
        'prompt',
        'full_prompt',
        'ai_response',
        'generated_news',
        'token_usage',
        'duration',
        'status',
        'error',
        'generated_at',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'token_usage' => 'array',
            'prompt_version' => 'integer',
            'duration' => 'integer',
            'generated_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function aiQueue(): BelongsTo
    {
        return $this->belongsTo(AiQueue::class);
    }

    public function prompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class);
    }

    public function rawTweet(): BelongsTo
    {
        return $this->belongsTo(RawTweet::class);
    }

    public function sourceAccount(): BelongsTo
    {
        return $this->belongsTo(SourceAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SourceCategory::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AiGenerationLog::class);
    }
}
