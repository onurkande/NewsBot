<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoryCluster extends Model
{
    use HasFactory;

    protected $fillable = [
        'cluster_hash',
        'title',
        'summary',
        'category_id',
        'main_source_account_id',
        'first_seen_at',
        'last_updated_at',
        'status',
        'story_score',
        'published_post_id',
    ];

    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_updated_at' => 'datetime',
            'story_score' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SourceCategory::class, 'category_id');
    }

    public function mainSourceAccount(): BelongsTo
    {
        return $this->belongsTo(SourceAccount::class, 'main_source_account_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoryClusterItem::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(StoryScore::class);
    }

    public function scanHistories(): HasMany
    {
        return $this->hasMany(ClusterScanHistory::class);
    }
}
