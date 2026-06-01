<?php

namespace App\Services\Admin\Twscrape;

use App\Models\TwscrapeLog;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;

class TwscrapeCommandService
{
    /**
     * Executes a twscrape CLI command.
     */
    public function execute(string $command): array
    {
        $scriptDir = (string) config('news_collection.twscrape.script_dir');
        $pythonPath = $this->pythonPath($scriptDir);
        $accountsDb = (string) config('news_collection.twscrape.accounts_db');

        $process = new Process([$pythonPath, '-m', 'twscrape', $command], $scriptDir);
        $process->setEnv(['TWSCRAPE_DB' => $accountsDb]);
        $process->setTimeout((int) config('news_collection.twscrape.timeout', 120));

        $startTime = microtime(true);
        $process->run();
        $durationMs = (int) ((microtime(true) - $startTime) * 1000);

        $isSuccessful = $process->isSuccessful();
        $output = $this->sanitizeUtf8(trim($process->getOutput()));
        $errorOutput = $this->sanitizeUtf8(trim($process->getErrorOutput()));

        // Log to database
        TwscrapeLog::create([
            'type' => 'command',
            'command' => $command,
            'output' => $output,
            'error' => $errorOutput,
            'is_successful' => $isSuccessful,
            'duration_ms' => $durationMs,
        ]);

        if (!$isSuccessful) {
            Log::error('Twscrape CLI Command Failed', [
                'command' => $command,
                'error' => $errorOutput
            ]);
        }

        return [
            'success' => $isSuccessful,
            'output' => $output ?: $errorOutput
        ];
    }

    /**
     * Windows konsolundan gelen çıktıyı güvenli UTF-8'e dönüştürür.
     */
    private function sanitizeUtf8(string $text): string
    {
        // Eğer zaten geçerli UTF-8 ise dokunma
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        // Windows Türkçe konsolda yaygın encoding'ler: CP1254 (Türkçe), CP850, CP1252
        $encodings = ['Windows-1254', 'Windows-1252', 'CP850', 'ISO-8859-9', 'ISO-8859-1'];
        foreach ($encodings as $enc) {
            $converted = mb_convert_encoding($text, 'UTF-8', $enc);
            if ($converted !== false) {
                return $converted;
            }
        }

        // Son çare: geçersiz byte'ları sil
        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }

    private function pythonPath(string $scriptDir): string
    {
        $configured = (string) config('news_collection.twscrape.python_path');
        $localVenv = $scriptDir.DIRECTORY_SEPARATOR.'.venv'.DIRECTORY_SEPARATOR.'Scripts'.DIRECTORY_SEPARATOR.'python.exe';

        if (($configured === '' || $configured === 'python') && file_exists($localVenv)) {
            return $localVenv;
        }

        return $configured ?: 'python';
    }
}
