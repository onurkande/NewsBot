<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiDraft extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_cluster_id',
        'prompt_text',
        'ai_response',
        'draft_title',
        'draft_body',
        'draft_status',
        'generated_at',
    ];

    protected function casts(): array
    {
        return [
            'generated_at' => 'datetime',
        ];
    }

    public function storyCluster(): BelongsTo
    {
        return $this->belongsTo(StoryCluster::class);
    }
}
