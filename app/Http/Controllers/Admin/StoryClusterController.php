<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoryCluster\IndexRequest;
use App\Queries\Admin\StoryClusterQuery;
use Illuminate\View\View;

class StoryClusterController extends Controller
{
    public function index(IndexRequest $request, StoryClusterQuery $query): View
    {
        return view('admin.story-clusters.index', $query->forIndex($request->filters()));
    }
}
