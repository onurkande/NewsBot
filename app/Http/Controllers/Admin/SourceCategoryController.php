<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SourceCategory\BulkDestroyRequest;
use App\Http\Requests\Admin\SourceCategory\IndexRequest;
use App\Http\Requests\Admin\SourceCategory\StoreRequest;
use App\Http\Requests\Admin\SourceCategory\UpdateRequest;
use App\Models\SourceCategory;
use App\Queries\Admin\SourceCategoryQuery;
use App\Services\Admin\SourceCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SourceCategoryController extends Controller
{
    public function index(IndexRequest $request, SourceCategoryQuery $query): View
    {
        return view('admin.source-categories.index', $query->forIndex($request->filters()));
    }

    public function create(): View
    {
        return view('admin.source-categories.create', [
            'category' => new SourceCategory(),
        ]);
    }

    public function store(StoreRequest $request, SourceCategoryService $service): RedirectResponse
    {
        $service->create($request->validated());

        return redirect()
            ->route('admin.source-categories.index')
            ->with('success', 'Kategori oluşturuldu.');
    }

    public function edit(SourceCategory $sourceCategory): View
    {
        return view('admin.source-categories.edit', [
            'category' => $sourceCategory,
        ]);
    }

    public function update(UpdateRequest $request, SourceCategory $sourceCategory, SourceCategoryService $service): RedirectResponse
    {
        $service->update($sourceCategory, $request->validated());

        return redirect()
            ->route('admin.source-categories.index')
            ->with('success', 'Kategori güncellendi.');
    }

    public function destroy(SourceCategory $sourceCategory, SourceCategoryService $service): RedirectResponse
    {
        $service->delete($sourceCategory);

        return redirect()
            ->route('admin.source-categories.index')
            ->with('success', 'Kategori silindi.');
    }

    public function bulkDestroy(BulkDestroyRequest $request, SourceCategoryService $service): RedirectResponse
    {
        $deleted = $service->bulkDelete($request->validated('selected'));

        return redirect()
            ->route('admin.source-categories.index')
            ->with('success', sprintf('%d kayıt silindi.', $deleted));
    }
}
