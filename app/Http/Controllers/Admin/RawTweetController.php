<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RawTweet\IndexRequest;
use App\Models\RawTweet;
use App\Queries\Admin\RawTweetQuery;
use Illuminate\View\View;

class RawTweetController extends Controller
{
    public function index(IndexRequest $request, RawTweetQuery $query): View
    {
        return view('admin.raw-tweets.index', $query->forIndex($request->filters()));
    }

    public function show(RawTweet $rawTweet): View
    {
        $rawTweet->load([
            'sourceAccount.category',
            'normalizedText',
            'storyClusterItem.storyCluster',
            'poolBatchItems.poolBatch',
            'aiGenerations.aiQueue',
            'duplicateChecks.matchedTweet',
            'matchedDuplicates.rawTweet',
        ]);

        return view('admin.raw-tweets.show', compact('rawTweet'));
    }
}
