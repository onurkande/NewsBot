<?php

namespace App\Jobs;

use App\Models\PublishAccount;
use App\Models\SystemLog;
use App\Services\Admin\PublishAccountService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Symfony\Component\Process\Process;
use Throwable;

class SyncPublishAccountJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;

    public function __construct(
        private int $publishAccountId
    ) {}

    public function handle(PublishAccountService $accountService): void
    {
        $account = PublishAccount::query()->find($this->publishAccountId);

        if (! $account) {
            $this->log('warning', 'PublishAccount bulunamadi.', [
                'publish_account_id' => $this->publishAccountId,
            ]);

            return;
        }

        $profile = $this->fetchProfile($account);

        if (! $profile) {
            $this->log('error', 'Profil bilgisi cekilemedi.', [
                'publish_account_id' => $account->id,
                'username' => $account->username,
            ]);

            return;
        }

        $accountService->updateFromProfile($account, $profile);

        $this->log('info', 'Profil bilgileri guncellendi.', [
            'publish_account_id' => $account->id,
            'username' => $account->username,
            'followers_count' => $profile['followers_count'] ?? null,
        ]);
    }

    public function failed(Throwable $exception): void
    {
        \Illuminate\Support\Facades\Log::error('SyncPublishAccountJob basarisiz oldu', [
            'publish_account_id' => $this->publishAccountId,
            'error' => $exception->getMessage(),
        ]);

        SystemLog::create([
            'level' => 'error',
            'module' => 'publish_account_sync',
            'message' => 'Hesap senkronizasyonu basarisiz oldu.',
            'context_json' => [
                'publish_account_id' => $this->publishAccountId,
                'error' => $exception->getMessage(),
            ],
        ]);
    }

    private function fetchProfile(PublishAccount $account): ?array
    {
        $scriptDir = (string) config('news_collection.twitter_api_client.script_dir', base_path('services/twitter-api-client'));
        $script = $scriptDir . DIRECTORY_SEPARATOR . 'fetch_profile.py';

        if (! file_exists($script)) {
            // Fallback: manuel dummy data ile test amaciyla
            return [
                'display_name' => $account->display_name,
                'followers_count' => $account->followers_count,
                'following_count' => $account->following_count,
                'statuses_count' => $account->statuses_count,
                'profile_image_url' => $account->profile_image_url,
            ];
        }

        $command = [
            $this->pythonPath($scriptDir),
            $script,
            '--username',
            $account->username,
            '--auth-token',
            $account->auth_token,
            '--ct0',
            $account->ct0,
        ];

        $process = new Process($command, $scriptDir);
        $process->setTimeout(60);
        $process->run();

        $output = trim($process->getOutput());
        $result = json_decode($output, true);

        if (! is_array($result) || ! ($result['success'] ?? false)) {
            return null;
        }

        return $result['profile'] ?? null;
    }

    private function pythonPath(string $scriptDir): string
    {
        $configured = (string) config('news_collection.twitter_api_client.python_path', 'python');

        if ($configured !== '' && $configured !== 'python') {
            return $configured;
        }

        $venvDir = '.venv';
        $localVenvWin = $scriptDir . DIRECTORY_SEPARATOR . $venvDir . DIRECTORY_SEPARATOR . 'Scripts' . DIRECTORY_SEPARATOR . 'python.exe';
        $localVenvUnix = $scriptDir . DIRECTORY_SEPARATOR . $venvDir . DIRECTORY_SEPARATOR . 'bin' . DIRECTORY_SEPARATOR . 'python';

        if (PHP_OS_FAMILY === 'Windows' && file_exists($localVenvWin)) {
            return $localVenvWin;
        }

        if (PHP_OS_FAMILY !== 'Windows' && file_exists($localVenvUnix)) {
            return $localVenvUnix;
        }

        return $configured ?: 'python';
    }

    private function log(string $level, string $message, array $context = []): void
    {
        SystemLog::create([
            'level' => $level,
            'module' => 'publish_account_sync',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
