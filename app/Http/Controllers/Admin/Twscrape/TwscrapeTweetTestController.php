<?php

namespace App\Http\Controllers\Admin\Twscrape;

use App\Http\Controllers\Controller;
use App\Services\NewsCollection\TwscrapeClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TwscrapeTweetTestController extends Controller
{
    public function index(): View
    {
        return view('admin.twscrape.tweet-test');
    }

    public function fetch(Request $request, TwscrapeClient $client): RedirectResponse
    {
        $request->validate([
            'url' => ['required', 'string', 'max:500'],
        ]);

        $url = $request->input('url');
        $result = null;
        $error = null;

        try {
            $result = $this->fetchTweet($client, $url);
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        return redirect()
            ->route('admin.twscrape.tweet-test.index')
            ->with('test_result', [
                'success' => $result !== null,
                'url' => $url,
                'error' => $error,
                'tweet' => $result,
            ]);
    }

    private function fetchTweet(TwscrapeClient $client, string $url): array
    {
        $scriptDir = (string) config('news_collection.twscrape.script_dir');
        $script = $scriptDir . DIRECTORY_SEPARATOR . 'fetch_tweet_by_url.py';

        if (! file_exists($script)) {
            throw new \RuntimeException('fetch_tweet_by_url.py bulunamadi: ' . $script);
        }

        $accountsDb = (string) config('news_collection.twscrape.accounts_db');

        $process = new \Symfony\Component\Process\Process([
            $client->pythonPath($scriptDir),
            $script,
            '--url',
            $url,
            '--accounts-db',
            $accountsDb,
        ], $scriptDir);

        $process->setTimeout(60);
        $process->setEnv($client->buildProcessEnv());
        $process->run();

        if (! $process->isSuccessful()) {
            $errorOutput = trim($process->getErrorOutput()) ?: 'twscrape command failed.';
            throw new \RuntimeException($errorOutput);
        }

        $payload = json_decode($process->getOutput(), true);

        if (! is_array($payload) || ! ($payload['success'] ?? false)) {
            $error = $payload['error'] ?? 'Unknown twscrape tweet fetch error.';
            throw new \RuntimeException($error);
        }

        return $payload['tweet'] ?? [];
    }
}
