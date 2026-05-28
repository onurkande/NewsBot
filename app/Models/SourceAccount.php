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
        'is_active',
        'last_checked_at',
        'last_seen_tweet_id',
        'trust_score',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_checked_at' => 'datetime',
            'priority_score' => 'integer',
            'check_interval_minutes' => 'integer',
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

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForCheck(Builder $query): Builder
    {
        return $query->active()
            ->where(function (Builder $query) {
                $query->whereNull('last_checked_at')
                    ->orWhereRaw('last_checked_at <= DATE_SUB(NOW(), INTERVAL check_interval_minutes MINUTE)');
            });
    }
}
