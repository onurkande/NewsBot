<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SourceAccount\BulkDestroyRequest;
use App\Http\Requests\Admin\SourceAccount\IndexRequest;
use App\Http\Requests\Admin\SourceAccount\StoreRequest;
use App\Http\Requests\Admin\SourceAccount\UpdateRequest;
use App\Jobs\FetchSourceAccountTweets;
use App\Models\SourceAccount;
use App\Models\SourceCategory;
use App\Queries\Admin\SourceAccountQuery;
use App\Services\Admin\SourceAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SourceAccountController extends Controller
{
    public function index(IndexRequest $request, SourceAccountQuery $query): View
    {
        return view('admin.source-accounts.index', $query->forIndex($request->filters()));
    }

    public function create(): View
    {
        return view('admin.source-accounts.create', [
            'account' => new SourceAccount([
                'is_active' => true,
                'priority_score' => 50,
                'trust_score' => 50,
                'check_interval_minutes' => 15,
            ]),
            'categories' => SourceCategory::orderBy('name')->get(),
        ]);
    }

    public function store(StoreRequest $request, SourceAccountService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()
            ->route('admin.source-accounts.index')
            ->with('success', 'Kaynak hesap oluşturuldu.');
    }

    public function edit(SourceAccount $sourceAccount): View
    {
        return view('admin.source-accounts.edit', [
            'account' => $sourceAccount,
            'categories' => SourceCategory::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateRequest $request, SourceAccount $sourceAccount, SourceAccountService $service): RedirectResponse
    {
        $service->update($sourceAccount, $request->validated());

        return redirect()
            ->route('admin.source-accounts.index')
            ->with('success', 'Kaynak hesap güncellendi.');
    }

    public function destroy(SourceAccount $sourceAccount, SourceAccountService $service): RedirectResponse
    {
        $service->delete($sourceAccount);

        return redirect()
            ->route('admin.source-accounts.index')
            ->with('success', 'Kaynak hesap silindi.');
    }

    public function bulkDestroy(BulkDestroyRequest $request, SourceAccountService $service): RedirectResponse
    {
        $deleted = $service->bulkDelete($request->validated('selected'));

        return redirect()
            ->route('admin.source-accounts.index')
            ->with('success', sprintf('%d kayıt silindi.', $deleted));
    }

    public function fetch(SourceAccount $sourceAccount): RedirectResponse
    {
        FetchSourceAccountTweets::dispatch($sourceAccount->id);

        return redirect()
            ->route('admin.source-accounts.index')
            ->with('success', '@' . $sourceAccount->username . ' toplama işine eklendi.');
    }
}
