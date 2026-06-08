<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishQueue\IndexRequest;
use App\Models\PublishQueue;
use App\Queries\Admin\PublishQueueQuery;
use App\Services\Admin\PublishQueueService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublishQueueController extends Controller
{
    public function index(IndexRequest $request, PublishQueueQuery $query): View
    {
        return view('admin.publish-queue.index', $query->forIndex($request->filters()));
    }

    public function show(PublishQueue $publishQueue): View
    {
        $publishQueue->load(['aiGeneration.rawTweet.sourceAccount.category', 'publishAccount', 'publishLogs']);

        return view('admin.publish-queue.show', [
            'item' => $publishQueue,
        ]);
    }

    public function retry(PublishQueue $publishQueue, PublishQueueService $service): RedirectResponse
    {
        try {
            $service->retry($publishQueue);

            return redirect()
                ->route('admin.publish-queue.show', $publishQueue)
                ->with('success', 'Yayin tekrar kuyruga alindi.');
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.publish-queue.show', $publishQueue)
                ->with('error', $e->getMessage());
        }
    }
}
