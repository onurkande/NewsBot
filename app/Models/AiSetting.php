<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'model_name',
        'retry_count',
        'timeout',
        'concurrent_jobs',
        'active_prompt_id',
        'opencode_api_key',
        'opencode_base_url',
        'opencode_model',
        'is_active',
        'auto_approve',
    ];

    protected function casts(): array
    {
        return [
            'retry_count' => 'integer',
            'timeout' => 'integer',
            'concurrent_jobs' => 'integer',
            'is_active' => 'boolean',
            'auto_approve' => 'boolean',
        ];
    }

    public static function singleton(): self
    {
        $settings = self::query()->first();

        if (! $settings) {
            $settings = self::query()->create([
                'provider' => 'gpt4free',
                'retry_count' => 3,
                'timeout' => 300,
                'concurrent_jobs' => 1,
                'opencode_base_url' => 'https://opencode.ai/zen/go/v1',
                'opencode_model' => 'deepseek-v4-flash',
                'is_active' => true,
                'auto_approve' => false,
            ]);
        }

        return $settings;
    }

    public function activePrompt(): BelongsTo
    {
        return $this->belongsTo(Prompt::class, 'active_prompt_id');
    }
}
