<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublishSettings extends Model
{
    use HasFactory;

    protected $table = 'publish_settings';

    protected $fillable = [
        'daily_post_limit',
        'hourly_post_limit',
        'min_delay_minutes',
        'max_delay_minutes',
        'publish_start_hour',
        'publish_end_hour',
        'min_gap_minutes',
        'auto_publish_enabled',
        'auto_scale_enabled',
        'follower_scale_enabled',
        'engagement_scale_enabled',
        'warmup_mode_enabled',
        'warmup_0_30_daily_limit',
        'warmup_30_60_daily_limit',
        'warmup_60_plus_daily_limit',
        'publish_expiration_hours',
    ];

    protected function casts(): array
    {
        return [
            'daily_post_limit' => 'integer',
            'hourly_post_limit' => 'integer',
            'min_delay_minutes' => 'integer',
            'max_delay_minutes' => 'integer',
            'publish_start_hour' => 'integer',
            'publish_end_hour' => 'integer',
            'min_gap_minutes' => 'integer',
            'auto_publish_enabled' => 'boolean',
            'auto_scale_enabled' => 'boolean',
            'follower_scale_enabled' => 'boolean',
            'engagement_scale_enabled' => 'boolean',
            'warmup_mode_enabled' => 'boolean',
            'warmup_0_30_daily_limit' => 'integer',
            'warmup_30_60_daily_limit' => 'integer',
            'warmup_60_plus_daily_limit' => 'integer',
            'publish_expiration_hours' => 'integer',
        ];
    }

    public static function singleton(): self
    {
        $settings = self::query()->first();

        if (! $settings) {
            $settings = self::query()->create([
                'daily_post_limit' => 10,
                'hourly_post_limit' => 1,
                'min_delay_minutes' => 45,
                'max_delay_minutes' => 90,
                'publish_start_hour' => 8,
                'publish_end_hour' => 23,
                'min_gap_minutes' => 3,
                'auto_publish_enabled' => false,
                'auto_scale_enabled' => false,
                'follower_scale_enabled' => false,
                'engagement_scale_enabled' => false,
                'warmup_mode_enabled' => false,
                'warmup_0_30_daily_limit' => 3,
                'warmup_30_60_daily_limit' => 5,
                'warmup_60_plus_daily_limit' => 10,
                'publish_expiration_hours' => 6,
            ]);
        }

        return $settings;
    }
}
