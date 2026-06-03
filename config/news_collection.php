<?php

return [
    'twscrape' => [
        'python_path' => env('TWSCRAPE_PYTHON_PATH') ?: 'python',
        'script_dir' => env('TWSCRAPE_SCRIPT_DIR') ?: base_path('services/twscrape'),
        'accounts_db' => env('TWSCRAPE_ACCOUNTS_DB') ?: base_path('services/twscrape/accounts.db'),
        'fetch_script' => env('TWSCRAPE_FETCH_SCRIPT') ?: 'fetch_user_tweets.py',
        'default_limit' => (int) env('TWSCRAPE_FETCH_LIMIT', 20),
        'initial_activation_limit' => (int) env('TWSCRAPE_INITIAL_ACTIVATION_LIMIT', 5),
        'timeout' => (int) env('TWSCRAPE_TIMEOUT', 120),
    ],

    'similarity' => [
        'threshold' => (float) env('NEWS_SIMILARITY_THRESHOLD', 72),
    ],

    'gpt4free' => [
        'python_path' => env('GPT4FREE_PYTHON_PATH') ?: 'python',
        'script_dir' => env('GPT4FREE_SCRIPT_DIR') ?: base_path('services/gpt4free'),
        'generate_script' => env('GPT4FREE_GENERATE_SCRIPT') ?: 'ai_generate.py',
        'provider_pool' => env('GPT4FREE_PROVIDER_POOL') ?: 'havuz',
        'timeout' => (int) env('GPT4FREE_TIMEOUT', 120),
    ],
];
