<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublishLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'publish_queue_id',
        'ai_generation_id',
        'publish_account_id',
        'status',
        'tweet_id_x',
        'error_message',
        'duration',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'duration' => 'integer',
            'published_at' => 'datetime',
        ];
    }

    public function publishQueue(): BelongsTo
    {
        return $this->belongsTo(PublishQueue::class);
    }

    public function aiGeneration(): BelongsTo
    {
        return $this->belongsTo(AiGeneration::class);
    }

    public function publishAccount(): BelongsTo
    {
        return $this->belongsTo(PublishAccount::class);
    }
}
