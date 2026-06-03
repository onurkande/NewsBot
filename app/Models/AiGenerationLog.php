<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiGenerationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ai_queue_id',
        'ai_generation_id',
        'level',
        'message',
        'context_json',
    ];

    protected function casts(): array
    {
        return [
            'context_json' => 'array',
        ];
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(AiQueue::class);
    }

    public function generation(): BelongsTo
    {
        return $this->belongsTo(AiGeneration::class);
    }
}
