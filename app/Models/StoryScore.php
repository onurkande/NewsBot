<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoryScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_cluster_id',
        'source_trust_score',
        'engagement_score',
        'recency_score',
        'velocity_score',
        'duplicate_penalty',
        'similarity_bonus',
        'final_score',
        'scored_at',
    ];

    protected function casts(): array
    {
        return [
            'source_trust_score' => 'decimal:2',
            'engagement_score' => 'decimal:2',
            'recency_score' => 'decimal:2',
            'velocity_score' => 'decimal:2',
            'duplicate_penalty' => 'decimal:2',
            'similarity_bonus' => 'decimal:2',
            'final_score' => 'decimal:2',
            'scored_at' => 'datetime',
        ];
    }

    public function storyCluster(): BelongsTo
    {
        return $this->belongsTo(StoryCluster::class);
    }
}
