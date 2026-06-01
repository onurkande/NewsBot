<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SourceAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'username',
        'display_name',
        'priority_score',
        'check_interval_minutes',
        'min_check_interval_minutes',
        'max_check_interval_minutes',
        'next_check_interval_minutes',
        'next_check_at',
        'is_active',
        'last_checked_at',
        'last_seen_tweet_id',
        'limited_initial_fetch_pending',
        'trust_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'limited_initial_fetch_pending' => 'boolean',
            'last_checked_at' => 'datetime',
            'next_check_at' => 'datetime',
            'priority_score' => 'integer',
            'check_interval_minutes' => 'integer',
            'min_check_interval_minutes' => 'integer',
            'max_check_interval_minutes' => 'integer',
            'next_check_interval_minutes' => 'integer',
            'trust_score' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(SourceCategory::class, 'category_id');
    }

    public function rawTweets(): HasMany
    {
        return $this->hasMany(RawTweet::class);
    }

    public function scanHistories(): HasMany
    {
        return $this->hasMany(ClusterScanHistory::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForCheck(Builder $query): Builder
    {
        return $query->active()
            ->where(function (Builder $query) {
                $query->whereNull('next_check_at')
                    ->orWhere('next_check_at', '<=', now());
            });
    }
}
