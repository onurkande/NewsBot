<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PoolSelection\IndexRequest;
use App\Queries\Admin\PoolSelectionQuery;
use Illuminate\View\View;

class PoolSelectionController extends Controller
{
    public function index(IndexRequest $request, PoolSelectionQuery $query): View
    {
        return view('admin.pool-selection.index', $query->forIndex($request->filters()));
    }
}
