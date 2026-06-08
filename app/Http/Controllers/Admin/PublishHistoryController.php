<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishQueue\IndexRequest;
use App\Queries\Admin\PublishLogQuery;
use Illuminate\View\View;

class PublishHistoryController extends Controller
{
    public function index(IndexRequest $request, PublishLogQuery $query): View
    {
        return view('admin.publish-history.index', $query->forIndex($request->filters()));
    }
}
