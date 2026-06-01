<?php

namespace App\Http\Requests\Admin\PoolSetting;

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
            'tweet_window_min' => ['required', 'integer', 'min:1', 'max:1440'],
            'tweet_window_max' => ['required', 'integer', 'min:1', 'max:1440', 'gte:tweet_window_min'],
            'selection_interval_min' => ['required', 'integer', 'min:1', 'max:1440'],
            'selection_interval_max' => ['required', 'integer', 'min:1', 'max:1440', 'gte:selection_interval_min'],
            'tweet_count_min' => ['required', 'integer', 'min:1', 'max:100'],
            'tweet_count_max' => ['required', 'integer', 'min:1', 'max:100', 'gte:tweet_count_min'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tweet_window_min.required' => 'Minimum tweet araligi zorunludur.',
            'tweet_window_max.required' => 'Maksimum tweet araligi zorunludur.',
            'tweet_window_max.gte' => 'Maksimum tweet araligi minimum degerden kucuk olamaz.',
            'selection_interval_min.required' => 'Minimum secim araligi zorunludur.',
            'selection_interval_max.required' => 'Maksimum secim araligi zorunludur.',
            'selection_interval_max.gte' => 'Maksimum secim araligi minimum degerden kucuk olamaz.',
            'tweet_count_min.required' => 'Minimum tweet sayisi zorunludur.',
            'tweet_count_max.required' => 'Maksimum tweet sayisi zorunludur.',
            'tweet_count_max.gte' => 'Maksimum tweet sayisi minimum degerden kucuk olamaz.',
        ];
    }

    public function attributes(): array
    {
        return [
            'tweet_window_min' => 'minimum tweet araligi',
            'tweet_window_max' => 'maksimum tweet araligi',
            'selection_interval_min' => 'minimum secim araligi',
            'selection_interval_max' => 'maksimum secim araligi',
            'tweet_count_min' => 'minimum tweet sayisi',
            'tweet_count_max' => 'maksimum tweet sayisi',
            'is_active' => 'aktif',
        ];
    }
}
