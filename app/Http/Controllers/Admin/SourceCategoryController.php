<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SourceCategoryController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->string('q'));
        $filter = $request->string('filter')->toString() ?: 'all';
        $sort = $request->string('sort')->toString() ?: 'name';
        $dir = strtolower($request->string('dir')->toString()) === 'desc' ? 'desc' : 'asc';

        $perPage = (int) $request->integer('per_page', 15);
        $perPageOptions = [15, 25, 50, 100];
        if (! in_array($perPage, $perPageOptions, true)) {
            $perPage = 15;
        }

        $query = SourceCategory::query()->withCount('sourceAccounts');

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($filter === 'with_sources') {
            $query->whereHas('sourceAccounts');
        } elseif ($filter === 'without_sources') {
            $query->whereDoesntHave('sourceAccounts');
        }

        $sortableColumns = [
            'name' => ['label' => 'Ad', 'column' => 'name'],
            'slug' => ['label' => 'Slug', 'column' => 'slug'],
            'sources' => ['label' => 'Kaynak', 'column' => 'source_accounts_count'],
            'description' => ['label' => 'Açıklama', 'column' => 'description'],
        ];

        $sortColumn = $sortableColumns[$sort]['column'] ?? 'name';
        $query->orderBy($sortColumn, $dir)->orderBy('id', 'desc');

        $categories = $query->paginate($perPage)->withQueryString();

        $categories->getCollection()->transform(function (SourceCategory $category) {
            $category->display_initials = $this->initials($category->name);
            $category->avatar_class = $this->avatarClass($category->id);
            $category->description_text = filled($category->description) ? $category->description : '—';
            $category->sources_label = number_format((int) $category->source_accounts_count) . ' hesap';

            return $category;
        });

        $baseQuery = $request->except(['page', 'sort', 'dir']);

        $sortColumns = [];
        foreach ($sortableColumns as $key => $config) {
            $sortColumns[$key] = [
                'label' => $config['label'],
                'url' => route('admin.source-categories.index', array_merge($baseQuery, [
                    'sort' => $key,
                    'dir' => $sort === $key && $dir === 'asc' ? 'desc' : 'asc',
                ])),
                'class' => $sort === $key ? ($dir === 'asc' ? 'sorted-asc' : 'sorted-desc') : '',
            ];
        }

        $filterOptions = [
            ['value' => 'all', 'label' => 'Tüm durumlar'],
            ['value' => 'with_sources', 'label' => 'Kaynağı olanlar'],
            ['value' => 'without_sources', 'label' => 'Kaynağı olmayanlar'],
        ];

        $pageSizeOptions = [15, 25, 50, 100];

        $pagination = $this->buildPaginationWindow($categories, $baseQuery);
        $summary = [
            'start' => $categories->count() ? $categories->firstItem() : 0,
            'end' => $categories->count() ? $categories->lastItem() : 0,
            'total' => $categories->total(),
        ];

        $activeFilters = ($search !== '' ? 1 : 0) + ($filter !== 'all' ? 1 : 0);

        return view('admin.source-categories.index', compact(
            'categories',
            'search',
            'filter',
            'sort',
            'dir',
            'perPage',
            'activeFilters',
            'filterOptions',
            'pageSizeOptions',
            'sortColumns',
            'pagination',
            'summary'
        ));
    }

    public function create(): View
    {
        return view('admin.source-categories.create', [
            'category' => new SourceCategory(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        SourceCategory::create($data);

        return redirect()->route('admin.source-categories.index')->with('success', 'Kategori oluşturuldu.');
    }

    public function edit(SourceCategory $sourceCategory): View
    {
        return view('admin.source-categories.edit', [
            'category' => $sourceCategory,
        ]);
    }

    public function update(Request $request, SourceCategory $sourceCategory): RedirectResponse
    {
        $data = $this->validated($request, $sourceCategory);
        $sourceCategory->update($data);

        return redirect()->route('admin.source-categories.index')->with('success', 'Kategori güncellendi.');
    }

    public function destroy(SourceCategory $sourceCategory): RedirectResponse
    {
        $sourceCategory->delete();

        return redirect()->route('admin.source-categories.index')->with('success', 'Kategori silindi.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = collect($data['ids'])
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $existingIds = SourceCategory::query()
            ->whereIn('id', $ids)
            ->pluck('id')
            ->all();

        if (count($existingIds) === 0) {
            return redirect()->route('admin.source-categories.index')->with('warning', 'Silinecek kategori bulunamadı.');
        }

        $deleted = SourceCategory::query()->whereIn('id', $existingIds)->delete();

        return redirect()->route('admin.source-categories.index')->with('success', $deleted . ' kategori silindi.');
    }

    private function validated(Request $request, ?SourceCategory $category = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        validator($data, [
            'slug' => ['required', 'string', 'max:255', Rule::unique('source_categories')->ignore($category?->id)],
        ])->validate();

        return $data;
    }

    private function initials(string $name): string
    {
        $parts = collect(preg_split('/\s+/', trim($name)) ?: [])
            ->filter()
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->take(2)
            ->values();

        if ($parts->isEmpty()) {
            return 'SC';
        }

        return $parts->join('');
    }

    private function avatarClass(int $id): string
    {
        $classes = ['ma-1', 'ma-2', 'ma-3', 'ma-4', 'ma-5', 'ma-6'];

        return $classes[$id % count($classes)];
    }

    private function buildPaginationWindow(LengthAwarePaginator $paginator, array $baseQuery): array
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();

        $makeUrl = static function (int $page) use ($baseQuery): string {
            return route('admin.source-categories.index', array_merge($baseQuery, ['page' => $page]));
        };

        $items = [];

        if ($lastPage <= 7) {
            for ($page = 1; $page <= $lastPage; $page++) {
                $items[] = [
                    'type' => 'page',
                    'page' => $page,
                    'url' => $makeUrl($page),
                    'active' => $page === $currentPage,
                ];
            }
        } else {
            $items[] = [
                'type' => 'page',
                'page' => 1,
                'url' => $makeUrl(1),
                'active' => $currentPage === 1,
            ];

            if ($currentPage > 4) {
                $items[] = ['type' => 'ellipsis'];
            }

            $start = max(2, $currentPage - 1);
            $end = min($lastPage - 2, $currentPage + 1);

            for ($page = $start; $page <= $end; $page++) {
                $items[] = [
                    'type' => 'page',
                    'page' => $page,
                    'url' => $makeUrl($page),
                    'active' => $page === $currentPage,
                ];
            }

            if ($currentPage < $lastPage - 3) {
                $items[] = ['type' => 'ellipsis'];
            }

            $items[] = [
                'type' => 'page',
                'page' => $lastPage,
                'url' => $makeUrl($lastPage),
                'active' => $currentPage === $lastPage,
            ];
        }

        return [
            'previousUrl' => $paginator->previousPageUrl(),
            'nextUrl' => $paginator->nextPageUrl(),
            'items' => $items,
        ];
    }
}
