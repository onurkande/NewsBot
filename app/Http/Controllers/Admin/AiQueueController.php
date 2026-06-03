<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AiQueue\IndexRequest;
use App\Models\AiQueue;
use App\Queries\Admin\AiQueueQuery;
use Illuminate\View\View;

class AiQueueController extends Controller
{
    public function index(IndexRequest $request, AiQueueQuery $query): View
    {
        return view('admin.ai-queue.index', $query->forIndex($request->filters()));
    }

    public function show(AiQueue $aiQueue): View
    {
        $aiQueue->load(['poolBatch', 'generations.rawTweet.sourceAccount', 'logs']);

        return view('admin.ai-queue.show', [
            'queue' => $aiQueue,
        ]);
    }
}
