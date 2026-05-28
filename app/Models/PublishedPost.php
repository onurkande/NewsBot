<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PublishedPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'story_cluster_id',
        'ai_draft_id',
        'x_post_id',
        'posted_text',
        'posted_at',
        'publish_status',
    ];

    protected function casts(): array
    {
        return [
            'posted_at' => 'datetime',
        ];
    }

    public function storyCluster(): BelongsTo
    {
        return $this->belongsTo(StoryCluster::class);
    }

    public function aiDraft(): BelongsTo
    {
        return $this->belongsTo(AiDraft::class);
    }
}
