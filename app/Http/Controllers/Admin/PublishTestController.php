<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishTest\StoreRequest;
use App\Models\PublishAccount;
use App\Models\SystemLog;
use App\Services\NewsCollection\PublishService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublishTestController extends Controller
{
    public function index(): View
    {
        return view('admin.publish-test.index', [
            'accounts' => PublishAccount::query()
                ->orderBy('username')
                ->get(),
        ]);
    }

    public function store(StoreRequest $request, PublishService $publishService): RedirectResponse
    {
        $account = PublishAccount::query()->find($request->input('publish_account_id'));

        if (! $account) {
            return redirect()
                ->route('admin.publish-test.index')
                ->with('error', 'Hesap bulunamadi.');
        }

        if (empty($account->auth_token) || empty($account->ct0)) {
            SystemLog::create([
                'level' => 'error',
                'module' => 'publish_test',
                'message' => 'Test paylasimi: hesap bilgileri eksik.',
                'context_json' => [
                    'publish_account_id' => $account->id,
                    'username' => $account->username,
                    'has_auth_token' => ! empty($account->auth_token),
                    'has_ct0' => ! empty($account->ct0),
                ],
            ]);

            return redirect()
                ->route('admin.publish-test.index')
                ->with('error', "Hesap bilgileri eksik: @{$account->username}. Lutfen auth_token ve ct0 alanlarini doldurun.");
        }

        if (! $account->is_active) {
            return redirect()
                ->route('admin.publish-test.index')
                ->with('error', "Hesap pasif durumda: @{$account->username}. Once aktif edin.");
        }

        $start = microtime(true);
        $result = null;
        $error = null;
        $raw = null;
        $tweetId = null;

        $mediaPaths = [];
        if ($request->input('media_paths')) {
            $decoded = json_decode($request->input('media_paths'), true);
            if (is_array($decoded)) {
                $mediaPaths = $decoded;
            }
        }

        SystemLog::create([
            'level' => 'info',
            'module' => 'publish_test',
            'message' => 'Test paylasimi baslatildi.',
            'context_json' => [
                'publish_account_id' => $account->id,
                'username' => $account->username,
                'text_length' => mb_strlen($request->input('text')),
                'media_count' => count($mediaPaths),
            ],
        ]);

        try {
            $result = $publishService->publish(
                $account,
                $request->input('text'),
                $mediaPaths
            );
            $tweetId = $result['tweet_id'] ?? null;

            SystemLog::create([
                'level' => 'info',
                'module' => 'publish_test',
                'message' => 'Test paylasimi basarili.',
                'context_json' => [
                    'publish_account_id' => $account->id,
                    'username' => $account->username,
                    'tweet_id' => $tweetId,
                    'duration_ms' => round((microtime(true) - $start) * 1000),
                ],
            ]);
        } catch (\Throwable $e) {
            $error = $e->getMessage();

            SystemLog::create([
                'level' => 'error',
                'module' => 'publish_test',
                'message' => 'Test paylasimi basarisiz.',
                'context_json' => [
                    'publish_account_id' => $account->id,
                    'username' => $account->username,
                    'error' => $error,
                    'duration_ms' => round((microtime(true) - $start) * 1000),
                ],
            ]);
        }

        $duration = (int) round((microtime(true) - $start) * 1000);

        return redirect()
            ->route('admin.publish-test.index')
            ->with('test_result', [
                'success' => $result !== null,
                'tweet_id' => $tweetId,
                'duration' => $duration,
                'error' => $error,
                'raw' => $result['raw'] ?? null,
                'account' => $account->username,
                'text' => $request->input('text'),
                'media_count' => count($mediaPaths),
            ])
            ->with('success', $result ? 'Tweet basariyla gonderildi!' : 'Tweet gonderilemedi.');
    }
}
