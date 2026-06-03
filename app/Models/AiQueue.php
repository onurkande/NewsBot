<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AiQueue extends Model
{
    use HasFactory;

    protected $fillable = [
        'pool_batch_id',
        'batch_no',
        'tweet_count',
        'story_score',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'tweet_count' => 'integer',
            'story_score' => 'decimal:2',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function poolBatch(): BelongsTo
    {
        return $this->belongsTo(PoolBatch::class);
    }

    public function generation(): HasOne
    {
        return $this->hasOne(AiGeneration::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(AiGenerationLog::class);
    }
}
