<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PoolSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'tweet_window_min',
        'tweet_window_max',
        'selection_interval_min',
        'selection_interval_max',
        'tweet_count_min',
        'tweet_count_max',
        'is_active',
        'next_run_at',
    ];

    protected function casts(): array
    {
        return [
            'tweet_window_min' => 'integer',
            'tweet_window_max' => 'integer',
            'selection_interval_min' => 'integer',
            'selection_interval_max' => 'integer',
            'tweet_count_min' => 'integer',
            'tweet_count_max' => 'integer',
            'is_active' => 'boolean',
            'next_run_at' => 'datetime',
        ];
    }

    /**
     * Singleton olarak tek satirlik ayar kaydini dondurur.
     * Yoksa varsayilan degerlerle olusturur.
     */
    public static function singleton(): self
    {
        $settings = self::query()->first();

        if (! $settings) {
            $settings = self::query()->create([
                'tweet_window_min' => 10,
                'tweet_window_max' => 40,
                'selection_interval_min' => 10,
                'selection_interval_max' => 15,
                'tweet_count_min' => 3,
                'tweet_count_max' => 5,
                'is_active' => true,
                'next_run_at' => null,
            ]);
        }

        return $settings;
    }
}
