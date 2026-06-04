<?php

namespace App\Services\NewsCollection;

use RuntimeException;
use Symfony\Component\Process\Process;

class Gpt4freeClient
{
    public function generate(string $prompt, ?string $model, int $timeout): array
    {
        $scriptDir = (string) config('news_collection.gpt4free.script_dir', base_path('services/gpt4free'));
        $script = $scriptDir . DIRECTORY_SEPARATOR . config('news_collection.gpt4free.generate_script', 'ai_generate.py');

        $command = [
            $this->pythonPath($scriptDir),
            $script,
            '--prompt',
            $prompt,
        ];

        if ($model) {
            $command[] = '--model';
            $command[] = $model;
        }

        $process = new Process($command, $scriptDir);
        $process->setTimeout($timeout);
        $process->setEnv($this->buildProcessEnv());

        $process->run();

        $output = trim($process->getOutput());
        $errorOutput = trim($process->getErrorOutput());

        $errorOutput = $this->sanitizeUtf8($errorOutput);
        $output = $this->sanitizeUtf8($output);

        if ($errorOutput) {
            \Illuminate\Support\Facades\Log::warning('Gpt4freeClient stderr', [
                'stderr' => $errorOutput,
            ]);
        }

        $result = json_decode($output, true);

        if (! is_array($result)) {
            \Illuminate\Support\Facades\Log::error('Gpt4freeClient invalid JSON', [
                'output' => $output,
                'error_output' => $errorOutput,
                'exit_code' => $process->getExitCode(),
            ]);

            throw new RuntimeException('GPT4Free geçersiz JSON yaniti dondurdu: ' . ($errorOutput ?: $output));
        }

        if (! ($result['success'] ?? false)) {
            $error = $result['error'] ?? 'Bilinmeyen GPT4Free hatasi';

            \Illuminate\Support\Facades\Log::error('Gpt4freeClient generation failed', [
                'error' => $error,
                'tried_providers' => $result['tried_providers'] ?? [],
            ]);

            throw new RuntimeException('GPT4Free uretimi basarisiz: ' . $error);
        }

        return [
            'raw' => $output,
            'content' => $result['content'] ?? '',
            'title' => $this->extractTitle($result['content'] ?? ''),
            'token_usage' => null,
            'provider' => $result['provider'] ?? null,
            'model' => $result['model'] ?? null,
        ];
    }

    private function extractTitle(string $content): ?string
    {
        if (preg_match('/BASLIK:\s*(.+)/i', $content, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    private function buildProcessEnv(): array
    {
        $env = [];

        $whitelist = [
            'PATH',
            'HOME',
            'USERPROFILE',
            'SYSTEMROOT',
            'WINDIR',
            'TMP',
            'TEMP',
            'APPDATA',
            'LOCALAPPDATA',
            'HOMEDRIVE',
            'HOMEPATH',
            'PROCESSOR_ARCHITECTURE',
            'PROCESSOR_IDENTIFIER',
            'PROCESSOR_LEVEL',
            'NUMBER_OF_PROCESSORS',
            'OS',
            'COMPUTERNAME',
            'PYTHONIOENCODING',
            'PYTHONUNBUFFERED',
            'PYTHONPATH',
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

        // GPT4Free HOME override
        $gptHome = (string) config('news_collection.gpt4free.home');

        if ($gptHome !== '') {
            $env['HOME'] = $gptHome;
            $env['XDG_CACHE_HOME'] = $gptHome.'/.cache';
            $env['XDG_CONFIG_HOME'] = $gptHome.'/.config';
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
        $configured = (string) config('news_collection.gpt4free.python_path', 'python');

        if ($configured !== '' && $configured !== 'python') {
            return $configured;
        }

        $venvDir = (string) config('news_collection.gpt4free.venv_dir', '.venv');
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
}
