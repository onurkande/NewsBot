<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublishQueue extends Model
{
    use HasFactory;

    protected $table = 'publish_queue';

    protected $fillable = [
        'ai_generation_id',
        'publish_account_id',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'error_message',
        'retry_count',
        'tweet_id_x',
        'duration',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'retry_count' => 'integer',
            'duration' => 'integer',
        ];
    }

    public function aiGeneration(): BelongsTo
    {
        return $this->belongsTo(AiGeneration::class);
    }

    public function publishAccount(): BelongsTo
    {
        return $this->belongsTo(PublishAccount::class);
    }

    public function publishLogs(): HasMany
    {
        return $this->hasMany(PublishLog::class);
    }
}
