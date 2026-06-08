<?php

namespace App\Services\Admin;

use App\Models\PublishAccount;
use App\Models\SystemLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class PublishAccountService
{
    public function __construct(
        private \App\Services\NewsCollection\TwscrapeClient $twscrapeClient,
    ) {}

    public function create(array $data): PublishAccount
    {
        return DB::transaction(function () use ($data) {
            return PublishAccount::create([
                'username' => Arr::get($data, 'username'),
                'display_name' => Arr::get($data, 'display_name'),
                'auth_token' => Arr::get($data, 'auth_token'),
                'ct0' => Arr::get($data, 'ct0'),
                'cookies_json' => Arr::get($data, 'cookies_json'),
                'account_created_at' => Arr::get($data, 'account_created_at') ? \Carbon\Carbon::parse(Arr::get($data, 'account_created_at')) : null,
                'is_active' => (bool) Arr::get($data, 'is_active', true),
            ]);
        });
    }

    public function update(PublishAccount $account, array $data): PublishAccount
    {
        return DB::transaction(function () use ($account, $data) {
            $account->update([
                'username' => Arr::get($data, 'username', $account->username),
                'display_name' => Arr::get($data, 'display_name', $account->display_name),
                'auth_token' => Arr::get($data, 'auth_token', $account->auth_token),
                'ct0' => Arr::get($data, 'ct0', $account->ct0),
                'cookies_json' => Arr::get($data, 'cookies_json', $account->cookies_json),
                'account_created_at' => Arr::get($data, 'account_created_at') ? \Carbon\Carbon::parse(Arr::get($data, 'account_created_at')) : $account->account_created_at,
                'is_active' => (bool) Arr::get($data, 'is_active', $account->is_active),
            ]);

            return $account->refresh();
        });
    }

    public function delete(PublishAccount $account): void
    {
        DB::transaction(function () use ($account) {
            $account->delete();
        });
    }

    public function syncProfile(PublishAccount $account): void
    {
        try {
            $profile = $this->twscrapeClient->fetchUserProfile($account->username);
            $this->updateFromProfile($account, $profile);

            SystemLog::create([
                'level' => 'info',
                'module' => 'publish_account_sync',
                'message' => 'Profil bilgileri twscrape ile guncellendi.',
                'context_json' => [
                    'publish_account_id' => $account->id,
                    'username' => $account->username,
                    'followers_count' => $profile['followers_count'] ?? null,
                ],
            ]);
        } catch (\Throwable $e) {
            SystemLog::create([
                'level' => 'error',
                'module' => 'publish_account_sync',
                'message' => 'Profil bilgisi twscrape ile cekilemedi.',
                'context_json' => [
                    'publish_account_id' => $account->id,
                    'username' => $account->username,
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }
    }

    public function updateFromProfile(PublishAccount $account, array $profile): void
    {
        $account->update([
            'display_name' => Arr::get($profile, 'display_name', $account->display_name),
            'followers_count' => (int) Arr::get($profile, 'followers_count', 0),
            'following_count' => (int) Arr::get($profile, 'following_count', 0),
            'statuses_count' => (int) Arr::get($profile, 'statuses_count', 0),
            'profile_image_url' => Arr::get($profile, 'profile_image_url'),
            'last_synced_at' => now(),
        ]);
    }
}
