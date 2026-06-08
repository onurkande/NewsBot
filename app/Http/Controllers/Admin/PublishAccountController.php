<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PublishAccount\BulkDestroyRequest;
use App\Http\Requests\Admin\PublishAccount\IndexRequest;
use App\Http\Requests\Admin\PublishAccount\StoreRequest;
use App\Http\Requests\Admin\PublishAccount\UpdateRequest;
use App\Models\PublishAccount;
use App\Queries\Admin\PublishAccountQuery;
use App\Services\Admin\PublishAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublishAccountController extends Controller
{
    public function index(IndexRequest $request, PublishAccountQuery $query): View
    {
        return view('admin.publish-accounts.index', $query->forIndex($request->filters()));
    }

    public function create(): View
    {
        return view('admin.publish-accounts.create');
    }

    public function store(StoreRequest $request, PublishAccountService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()
            ->route('admin.publish-accounts.index')
            ->with('success', 'Hesap olusturuldu.');
    }

    public function edit(PublishAccount $publishAccount): View
    {
        return view('admin.publish-accounts.edit', [
            'account' => $publishAccount,
        ]);
    }

    public function update(UpdateRequest $request, PublishAccount $publishAccount, PublishAccountService $service): RedirectResponse
    {
        $service->update($publishAccount, $request->validated());

        return redirect()
            ->route('admin.publish-accounts.index')
            ->with('success', 'Hesap guncellendi.');
    }

    public function destroy(PublishAccount $publishAccount, PublishAccountService $service): RedirectResponse
    {
        $service->delete($publishAccount);

        return redirect()
            ->route('admin.publish-accounts.index')
            ->with('success', 'Hesap silindi.');
    }

    public function bulkDestroy(BulkDestroyRequest $request, PublishAccountService $service): RedirectResponse
    {
        foreach ($request->validated()['ids'] as $id) {
            $account = PublishAccount::query()->find($id);
            if ($account) {
                $service->delete($account);
            }
        }

        return redirect()
            ->route('admin.publish-accounts.index')
            ->with('success', 'Secili hesaplar silindi.');
    }

    public function sync(PublishAccount $publishAccount, PublishAccountService $service): RedirectResponse
    {
        try {
            $service->syncProfile($publishAccount);

            return redirect()
                ->route('admin.publish-accounts.index')
                ->with('success', 'Hesap bilgileri twscrape ile guncellendi.');
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.publish-accounts.index')
                ->with('error', 'Profil bilgisi cekilemedi: ' . $e->getMessage());
        }
    }
}
