<?php
namespace App\Http\Controllers\Admin\Twscrape;

use App\Http\Controllers\Controller;
use App\Models\TwscrapeAccount;

class TwscrapeStatController extends Controller
{
    public function index()
    {
        $accounts = TwscrapeAccount::orderBy('total_usage', 'desc')->get();
        return view('admin.twscrape-stats.index', compact('accounts'));
    }
}
