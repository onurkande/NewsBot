<?php
namespace App\Http\Controllers\Admin\Twscrape;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Twscrape\IndexLogRequest;
use App\Queries\Admin\Twscrape\TwscrapeLogQuery;

class TwscrapeLogController extends Controller
{
    public function index(IndexLogRequest $request, TwscrapeLogQuery $query)
    {
        return view('admin.twscrape-logs.index', $query->forIndex($request->filters()));
    }
}
