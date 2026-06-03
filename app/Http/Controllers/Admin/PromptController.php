<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Prompt\BulkDestroyRequest;
use App\Http\Requests\Admin\Prompt\IndexRequest;
use App\Http\Requests\Admin\Prompt\StoreRequest;
use App\Http\Requests\Admin\Prompt\UpdateRequest;
use App\Models\Prompt;
use App\Models\SourceCategory;
use App\Queries\Admin\PromptQuery;
use App\Services\Admin\PromptService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PromptController extends Controller
{
    public function index(IndexRequest $request, PromptQuery $query): View
    {
        return view('admin.prompts.index', $query->forIndex($request->filters()));
    }

    public function create(): View
    {
        return view('admin.prompts.create', [
            'prompt' => new Prompt([
                'is_active' => false,
                'version' => 1,
            ]),
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function store(StoreRequest $request, PromptService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Prompt olusturuldu.');
    }

    public function edit(Prompt $prompt): View
    {
        $prompt->load('sourceCategory');

        return view('admin.prompts.edit', [
            'prompt' => $prompt,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(UpdateRequest $request, Prompt $prompt, PromptService $service): RedirectResponse
    {
        $service->update($prompt, $request->validated());

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Prompt guncellendi.');
    }

    public function destroy(Prompt $prompt, PromptService $service): RedirectResponse
    {
        $service->delete($prompt);

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', 'Prompt silindi.');
    }

    public function bulkDestroy(BulkDestroyRequest $request, PromptService $service): RedirectResponse
    {
        $deleted = $service->bulkDelete($request->validated('selected'));

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', sprintf('%d kayit silindi.', $deleted));
    }

    public function activate(Prompt $prompt, PromptService $service): RedirectResponse
    {
        $service->activate($prompt);

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', sprintf('"%s" promptu aktif edildi.', $prompt->name));
    }

    public function deactivate(Prompt $prompt, PromptService $service): RedirectResponse
    {
        $service->deactivate($prompt);

        return redirect()
            ->route('admin.prompts.index')
            ->with('success', sprintf('"%s" promptu deaktif edildi.', $prompt->name));
    }

    private function categoryOptions(): array
    {
        return SourceCategory::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->prepend('-- Kategorisiz (Global) --', '')
            ->toArray();
    }
}
