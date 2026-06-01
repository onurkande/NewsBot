<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PoolHistory\IndexRequest;
use App\Models\PoolBatch;
use App\Queries\Admin\PoolHistoryQuery;
use Illuminate\View\View;

class PoolHistoryController extends Controller
{
    public function index(IndexRequest $request, PoolHistoryQuery $query): View
    {
        return view('admin.pool-history.index', $query->forIndex($request->filters()));
    }

    public function show(PoolBatch $poolBatch): View
    {
        $poolBatch->load(['items.rawTweet.sourceAccount']);

        $selectedItems = $poolBatch->items->where('is_selected', true);
        $notSelectedItems = $poolBatch->items->where('is_selected', false);

        return view('admin.pool-history.show', [
            'batch' => $poolBatch,
            'selectedItems' => $selectedItems,
            'notSelectedItems' => $notSelectedItems,
        ]);
    }
}
