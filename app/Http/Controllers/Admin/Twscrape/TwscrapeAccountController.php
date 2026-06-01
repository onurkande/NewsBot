<?php
namespace App\Http\Controllers\Admin\Twscrape;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Twscrape\IndexAccountRequest;
use App\Http\Requests\Admin\Twscrape\UpdateWeightRequest;
use App\Models\TwscrapeAccount;
use App\Queries\Admin\Twscrape\TwscrapeAccountQuery;
use App\Services\Admin\Twscrape\TwscrapeAccountService;

class TwscrapeAccountController extends Controller
{
    public function index(IndexAccountRequest $request, TwscrapeAccountQuery $query, TwscrapeAccountService $service)
    {
        $service->syncAccounts();
        return view('admin.twscrape-accounts.index', $query->forIndex($request->filters()));
    }

    public function show($username)
    {
        $account = TwscrapeAccount::where('username', $username)->firstOrFail();
        $sqliteAccount = $account->sqliteAccount();
        
        return view('admin.twscrape-accounts.show', compact('account', 'sqliteAccount'));
    }

    public function toggle($username, TwscrapeAccountService $service)
    {
        $service->toggleStatus($username);
        return redirect()->back()->with('success', "Hesap durumu güncellendi: {$username}");
    }

    public function updateWeight($username, UpdateWeightRequest $request, TwscrapeAccountService $service)
    {
        $service->updateWeight($username, $request->weight);
        return redirect()->back()->with('success', "Hesap ağırlığı güncellendi: {$username}");
    }
}
