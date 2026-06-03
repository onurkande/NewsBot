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
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'token_usage' => 'array',
            'prompt_version' => 'integer',
            'duration' => 'integer',
            'generated_at' => 'datetime',
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

    public function items(): HasMany
    {
        return $this->hasMany(AiGenerationItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AiGenerationLog::class);
    }
}
