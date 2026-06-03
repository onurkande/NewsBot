<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiGeneration\IndexRequest;
use App\Models\AiGeneration;
use App\Queries\Admin\AiGenerationQuery;
use App\Services\NewsCollection\AIReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AiGenerationController extends Controller
{
    public function index(IndexRequest $request, AiGenerationQuery $query): View
    {
        return view('admin.ai-generations.index', $query->forIndex($request->filters()));
    }

    public function show(AiGeneration $aiGeneration): View
    {
        $aiGeneration->load(['aiQueue', 'prompt', 'rawTweet.sourceAccount.category', 'category', 'logs']);

        return view('admin.ai-generations.show', [
            'generation' => $aiGeneration,
        ]);
    }

    public function approve(AiGeneration $aiGeneration, AIReviewService $reviewService): RedirectResponse
    {
        try {
            $reviewService->approve($aiGeneration);

            return redirect()
                ->route('admin.ai-generations.show', $aiGeneration)
                ->with('success', 'Uretim onaylandi.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.ai-generations.show', $aiGeneration)
                ->with('error', $e->getMessage());
        }
    }

    public function reject(AiGeneration $aiGeneration, AIReviewService $reviewService): RedirectResponse
    {
        try {
            $reviewService->reject($aiGeneration);

            return redirect()
                ->route('admin.ai-generations.show', $aiGeneration)
                ->with('success', 'Uretim reddedildi.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.ai-generations.show', $aiGeneration)
                ->with('error', $e->getMessage());
        }
    }

    public function publish(AiGeneration $aiGeneration, AIReviewService $reviewService): RedirectResponse
    {
        try {
            $reviewService->publish($aiGeneration);

            return redirect()
                ->route('admin.ai-generations.show', $aiGeneration)
                ->with('success', 'Uretim yayina alindi.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.ai-generations.show', $aiGeneration)
                ->with('error', $e->getMessage());
        }
    }
}
