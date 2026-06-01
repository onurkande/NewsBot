<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScanHistory\IndexRequest;
use App\Queries\Admin\ScanHistoryQuery;
use Illuminate\View\View;

class ScanHistoryController extends Controller
{
    public function index(IndexRequest $request, ScanHistoryQuery $query): View
    {
        return view('admin.scan-histories.index', $query->forIndex($request->filters()));
    }
}
