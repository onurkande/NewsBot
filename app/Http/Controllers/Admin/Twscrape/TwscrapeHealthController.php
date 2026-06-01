<?php
namespace App\Http\Controllers\Admin\Twscrape;

use App\Http\Controllers\Controller;
use App\Services\Admin\Twscrape\TwscrapeHealthService;

class TwscrapeHealthController extends Controller
{
    public function index(TwscrapeHealthService $service)
    {
        $stats = $service->getHealthStats();
        return view('admin.twscrape-health.index', compact('stats'));
    }
}
