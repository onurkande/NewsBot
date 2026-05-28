<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RawTweet;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RawTweetController extends Controller
{
    public function index(Request $request): View
    {
        $tweets = RawTweet::query()
            ->with(['sourceAccount.category', 'storyClusterItem.storyCluster'])
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where('tweet_text', 'like', "%{$search}%")
                    ->orWhere('tweet_id', 'like', "%{$search}%");
            })
            ->latest('tweeted_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.raw-tweets.index', compact('tweets'));
    }
}
