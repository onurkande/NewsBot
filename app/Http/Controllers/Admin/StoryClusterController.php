<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoryCluster\IndexRequest;
use App\Models\StoryCluster;
use App\Queries\Admin\ScanHistoryQuery;
use App\Queries\Admin\StoryClusterQuery;
use Illuminate\View\View;

class StoryClusterController extends Controller
{
    public function index(IndexRequest $request, StoryClusterQuery $query): View
    {
        return view('admin.story-clusters.index', $query->forIndex($request->filters()));
    }

    public function show(StoryCluster $storyCluster, ScanHistoryQuery $scanHistoryQuery): View
    {
        $storyCluster->load(['category', 'mainSourceAccount']);

        return view('admin.story-clusters.show', [
            'cluster' => $storyCluster,
            ...$scanHistoryQuery->forCluster($storyCluster),
        ]);
    }
}
