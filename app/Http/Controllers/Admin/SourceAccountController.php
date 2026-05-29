<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\FetchSourceAccountTweets;
use App\Models\SourceAccount;
use App\Models\SourceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SourceAccountController extends Controller
{
    public function index(Request $request): View
    {
        $accounts = SourceAccount::query()
            ->with('category')
            ->when($request->string('q')->toString(), function ($query, string $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%")
                        ->orWhere('display_name', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), fn ($query) => $query->where('is_active', $request->input('status') === 'active'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.source-accounts.index', compact('accounts'));
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

    public function store(Request $request): RedirectResponse
    {
        SourceAccount::create($this->validated($request));

        return redirect()->route('admin.source-accounts.index')->with('success', 'Kaynak hesap olusturuldu.');
    }

    public function edit(SourceAccount $sourceAccount): View
    {
        return view('admin.source-accounts.edit', [
            'account' => $sourceAccount,
            'categories' => SourceCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, SourceAccount $sourceAccount): RedirectResponse
    {
        $sourceAccount->update($this->validated($request, $sourceAccount));

        return redirect()->route('admin.source-accounts.index')->with('success', 'Kaynak hesap guncellendi.');
    }

    public function destroy(SourceAccount $sourceAccount): RedirectResponse
    {
        $sourceAccount->delete();

        return redirect()->route('admin.source-accounts.index')->with('success', 'Kaynak hesap silindi.');
    }

    public function fetch(SourceAccount $sourceAccount): RedirectResponse
    {
        FetchSourceAccountTweets::dispatch($sourceAccount->id);

        return redirect()->route('admin.source-accounts.index')->with('success', '@'.$sourceAccount->username.' toplama isine eklendi.');
    }

    private function validated(Request $request, ?SourceAccount $account = null): array
    {
        $request->merge([
            'username' => ltrim((string) $request->input('username'), '@'),
        ]);

        $data = $request->validate([
            'category_id' => ['nullable', 'exists:source_categories,id'],
            'username' => ['required', 'string', 'max:255', Rule::unique('source_accounts')->ignore($account?->id)],
            'display_name' => ['nullable', 'string', 'max:255'],
            'priority_score' => ['required', 'integer', 'min:0', 'max:100'],
            'check_interval_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
            'trust_score' => ['required', 'integer', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
