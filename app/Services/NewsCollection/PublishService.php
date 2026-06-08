<?php

namespace App\Services\NewsCollection;

use App\Models\PublishAccount;
use App\Models\SystemLog;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Symfony\Component\Process\Process;

class PublishService
{
    public function publish(PublishAccount $account, string $text, array $mediaPaths = []): array
    {
        $startTime = microtime(true);

        if (empty($account->auth_token) || empty($account->ct0)) {
            $this->log('error', 'Hesap bilgileri eksik (auth_token veya ct0).', [
                'publish_account_id' => $account->id,
                'username' => $account->username,
                'has_auth_token' => ! empty($account->auth_token),
                'has_ct0' => ! empty($account->ct0),
            ]);
            throw new RuntimeException("Hesap bilgileri eksik: @{$account->username}. Lutfen auth_token ve ct0 alanlarini kontrol edin.");
        }

        $payload = [
            'text' => $text,
            'media_paths' => $mediaPaths,
            'cookies' => [
                'auth_token' => $account->auth_token,
                'ct0' => $account->ct0,
            ],
        ];

        $payloadFile = $this->writePayloadFile($payload);

        try {
            $result = $this->runScript($payloadFile);
        } finally {
            $this->cleanupPayloadFile($payloadFile);
        }

        $duration = (int) round((microtime(true) - $startTime) * 1000);

        if (! ($result['success'] ?? false)) {
            $error = $result['error'] ?? 'Bilinmeyen Twitter publish hatasi';
            $this->log('error', 'Twitter paylasimi basarisiz oldu.', [
                'publish_account_id' => $account->id,
                'error' => $error,
            ]);
            throw new RuntimeException('Twitter paylasimi basarisiz: ' . $error);
        }

        $this->log('info', 'Twitter paylasimi basarili.', [
            'publish_account_id' => $account->id,
            'tweet_id' => $result['tweet_id'] ?? null,
            'duration_ms' => $duration,
        ]);

        return [
            'tweet_id' => $result['tweet_id'] ?? null,
            'duration' => $duration,
            'raw' => $result['raw'] ?? null,
        ];
    }

    private function runScript(string $payloadFile): array
    {
        $scriptDir = (string) config('news_collection.twitter_api_client.script_dir', base_path('services/twitter-api-client'));
        $script = $scriptDir . DIRECTORY_SEPARATOR . config('news_collection.twitter_api_client.publish_script', 'publish_tweet.py');
        $timeout = (int) config('news_collection.twitter_api_client.timeout', 120);

        $command = [
            $this->pythonPath($scriptDir),
            $script,
            '--payload-file',
            $payloadFile,
        ];

        $process = new Process($command, $scriptDir);
        $process->setTimeout($timeout);
        $process->setEnv($this->buildProcessEnv());

        $process->run();

        $output = trim($process->getOutput());
        $errorOutput = trim($process->getErrorOutput());

        $output = $this->sanitizeUtf8($output);
        $errorOutput = $this->sanitizeUtf8($errorOutput);

        $result = json_decode($output, true);

        if (! is_array($result)) {
            \Illuminate\Support\Facades\Log::error('PublishService invalid JSON', [
                'output' => $output,
                'error_output' => $errorOutput,
                'exit_code' => $process->getExitCode(),
            ]);

            throw new RuntimeException('Twitter publish geçersiz JSON yaniti: ' . ($errorOutput ?: $output));
        }

        return $result;
    }

    private function writePayloadFile(array $payload): string
    {
        $tmpDir = storage_path('app/temp');
        if (! is_dir($tmpDir)) {
            mkdir($tmpDir, 0755, true);
        }

        $path = $tmpDir . DIRECTORY_SEPARATOR . 'publish_payload_' . uniqid() . '.json';
        file_put_contents($path, json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $path;
    }

    private function cleanupPayloadFile(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    private function buildProcessEnv(): array
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

    private function sanitizeUtf8(string $text): string
    {
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        $encodings = ['Windows-1254', 'Windows-1252', 'CP850', 'ISO-8859-9', 'ISO-8859-1'];
        foreach ($encodings as $enc) {
            $converted = @mb_convert_encoding($text, 'UTF-8', $enc);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                return $converted;
            }
        }

        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
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
            'module' => 'publish',
            'message' => $message,
            'context_json' => $context,
        ]);
    }
}
