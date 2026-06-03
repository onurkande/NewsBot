<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiSetting\UpdateRequest;
use App\Models\AiSetting;
use App\Models\Prompt;
use App\Services\Admin\AiSettingService;
use App\Services\NewsCollection\AITestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AiSettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.ai-settings.edit', [
            'settings' => AiSetting::singleton(),
            'prompts' => Prompt::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(),
            'providers' => [
                'gpt4free' => 'GPT4Free',
                'opencode' => 'OpenCode',
            ],
        ]);
    }

    public function update(UpdateRequest $request, AiSettingService $service): RedirectResponse
    {
        $service->update(AiSetting::singleton(), $request->validated());

        return redirect()
            ->route('admin.ai-settings.edit')
            ->with('success', 'AI ayarlari guncellendi.');
    }

    public function test(Request $request, AITestService $testService): JsonResponse
    {
        $request->validate([
            'provider' => ['required', 'string', 'in:gpt4free,opencode'],
            'prompt' => ['required', 'string', 'min:1', 'max:2000'],
        ]);

        $result = $testService->test($request->input('provider'), $request->input('prompt'));

        // Ensure error message is valid UTF-8 before JSON encoding
        if (! $result['success'] && isset($result['error'])) {
            $result['error'] = $this->sanitizeUtf8($result['error']);
        }

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /**
     * Windows stderr'dan gelen Turkce karakterleri guvenli UTF-8'e donusturur.
     */
    private function sanitizeUtf8(string $text): string
    {
        if (mb_check_encoding($text, 'UTF-8')) {
            return $text;
        }

        $encodings = ['Windows-1254', 'Windows-1252', 'CP850', 'ISO-8859-9', 'ISO-8859-1'];
        foreach ($encodings as $enc) {
            $converted = mb_convert_encoding($text, 'UTF-8', $enc);
            if ($converted !== false && mb_check_encoding($converted, 'UTF-8')) {
                return $converted;
            }
        }

        return mb_convert_encoding($text, 'UTF-8', 'UTF-8');
    }
}
