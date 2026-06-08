<?php

namespace App\Http\Requests\Admin\PublishSettings;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'daily_post_limit' => ['required', 'integer', 'min:1', 'max:500'],
            'hourly_post_limit' => ['required', 'integer', 'min:1', 'max:24'],
            'min_delay_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'max_delay_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'publish_start_hour' => ['required', 'integer', 'min:0', 'max:23'],
            'publish_end_hour' => ['required', 'integer', 'min:0', 'max:24'],
            'min_gap_minutes' => ['required', 'integer', 'min:0', 'max:1440'],
            'auto_publish_enabled' => ['sometimes', 'boolean'],
            'auto_scale_enabled' => ['sometimes', 'boolean'],
            'follower_scale_enabled' => ['sometimes', 'boolean'],
            'engagement_scale_enabled' => ['sometimes', 'boolean'],
            'warmup_mode_enabled' => ['sometimes', 'boolean'],
            'warmup_0_30_daily_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'warmup_30_60_daily_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'warmup_60_plus_daily_limit' => ['required', 'integer', 'min:1', 'max:100'],
            'publish_expiration_hours' => ['required', 'integer', 'min:0', 'max:168'],
        ];
    }
}
