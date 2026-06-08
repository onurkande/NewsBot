<?php

namespace App\Services\NewsCollection;

use App\Models\SourceAccount;
use RuntimeException;
use Symfony\Component\Process\Process;

class TwscrapeClient
{
    public function fetchUserTweets(SourceAccount $account, ?int $limit = null): array
    {
        $scriptDir = (string) config('news_collection.twscrape.script_dir');
        $script = $scriptDir.DIRECTORY_SEPARATOR.config('news_collection.twscrape.fetch_script');
        $accountsDb = (string) config('news_collection.twscrape.accounts_db');

        $process = new Process([
            $this->pythonPath($scriptDir),
            $script,
            '--username',
            $account->username,
            '--limit',
            (string) ($limit ?: config('news_collection.twscrape.default_limit')),
            '--accounts-db',
            $accountsDb,
        ], $scriptDir);

        $process->setTimeout((int) config('news_collection.twscrape.timeout'));
        $process->setEnv($this->buildProcessEnv());
        $process->run();

        if (! $process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput()) ?: 'twscrape command failed.';
            \Illuminate\Support\Facades\Log::error('TwscrapeClient process failed', [
                'username' => $account->username,
                'error' => $errorOutput,
                'command' => $process->getCommandLine(),
            ]);
            throw new RuntimeException($errorOutput);
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload) || ! array_key_exists('tweets', $payload)) {
            \Illuminate\Support\Facades\Log::error('TwscrapeClient invalid payload', [
                'username' => $account->username,
                'output' => $process->getOutput(),
            ]);
            throw new RuntimeException('twscrape did not return a valid JSON payload.');
        }

        return $payload;
    }

    public function fetchUserProfile(string $username): array
    {
        $scriptDir = (string) config('news_collection.twscrape.script_dir');
        $script = $scriptDir.DIRECTORY_SEPARATOR.'fetch_user_profile.py';
        $accountsDb = (string) config('news_collection.twscrape.accounts_db');

        $process = new Process([
            $this->pythonPath($scriptDir),
            $script,
            '--username',
            $username,
            '--accounts-db',
            $accountsDb,
        ], $scriptDir);

        $process->setTimeout(60);
        $process->setEnv($this->buildProcessEnv());
        $process->run();

        if (! $process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput()) ?: 'twscrape profile fetch failed.';
            \Illuminate\Support\Facades\Log::error('TwscrapeClient fetchUserProfile failed', [
                'username' => $username,
                'error' => $errorOutput,
            ]);
            throw new RuntimeException($errorOutput);
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload) || ! ($payload['success'] ?? false)) {
            $error = $payload['error'] ?? 'Unknown twscrape profile fetch error.';
            \Illuminate\Support\Facades\Log::error('TwscrapeClient invalid profile payload', [
                'username' => $username,
                'output' => $process->getOutput(),
            ]);
            throw new RuntimeException($error);
        }

        return $payload['profile'] ?? [];
    }

    public function pythonPath(string $scriptDir): string
    {
        $configured = (string) config('news_collection.twscrape.python_path');
        $localVenv = $scriptDir.DIRECTORY_SEPARATOR.'.venv'.DIRECTORY_SEPARATOR.'Scripts'.DIRECTORY_SEPARATOR.'python.exe';

        if (($configured === '' || $configured === 'python') && file_exists($localVenv)) {
            return $localVenv;
        }

        return $configured ?: 'python';
    }

    public function buildProcessEnv(): array
    {
        $env = [];
        $whitelist = [
            'PATH', 'HOME', 'USERPROFILE', 'SYSTEMROOT', 'WINDIR',
            'TMP', 'TEMP', 'APPDATA', 'LOCALAPPDATA',
            'HOMEDRIVE', 'HOMEPATH', 'PYTHONIOENCODING', 'PYTHONUNBUFFERED', 'PYTHONPATH',
        ];

        foreach ($whitelist as $key) {
            $value = getenv($key);
            if ($value !== false && $value !== '') {
                $env[$key] = $value;
            }
        }

        $env['PYTHONIOENCODING'] = 'utf-8';
        $env['PYTHONUNBUFFERED'] = '1';

        if (PHP_OS_FAMILY === 'Windows') {
            $systemRoot = $env['SYSTEMROOT'] ?? $env['WINDIR'] ?? 'C:\\Windows';
            $env['SYSTEMROOT'] = $systemRoot;
            $env['WINDIR'] = $env['WINDIR'] ?? $systemRoot;
        }

        if (empty($env['PATH'])) {
            $env['PATH'] = PHP_OS_FAMILY === 'Windows'
                ? 'C:\\Windows\\system32;C:\\Windows;C:\\Windows\\System32\\Wbem'
                : '/usr/local/bin:/usr/bin:/bin';
        }

        return $env;
    }
}
