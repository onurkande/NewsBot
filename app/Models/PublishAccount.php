<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PublishAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'display_name',
        'followers_count',
        'following_count',
        'statuses_count',
        'profile_image_url',
        'is_active',
        'last_synced_at',
        'account_created_at',
        'auth_token',
        'ct0',
        'cookies_json',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'followers_count' => 'integer',
            'following_count' => 'integer',
            'statuses_count' => 'integer',
            'last_synced_at' => 'datetime',
            'account_created_at' => 'datetime',
            'cookies_json' => 'array',
        ];
    }

    public function publishQueues(): HasMany
    {
        return $this->hasMany(PublishQueue::class);
    }

    public function publishLogs(): HasMany
    {
        return $this->hasMany(PublishLog::class);
    }
}
