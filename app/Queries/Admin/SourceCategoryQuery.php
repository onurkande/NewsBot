<?php

namespace App\Queries\Admin;

use App\Models\SourceCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as Paginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SourceCategoryQuery
{
    private const DEFAULT_SORT = 'name';
    private const DEFAULT_DIR = 'asc';
    private const DEFAULT_PER_PAGE = 15;

    public function forIndex(array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->paginate($filters);

        return [
            'categories' => $paginator,
            'search' => $filters['q'],
            'filter' => $filters['filter'],
            'sort' => $filters['sort'],
            'dir' => $filters['dir'],
            'perPage' => $filters['per_page'],
            'filterOptions' => $this->filterOptions(),
            'pageSizeOptions' => $this->pageSizeOptions(),
            'sortColumns' => $this->sortColumns($filters),
            'summary' => $this->summary($paginator),
            'pagination' => $this->paginationWindow($paginator, $filters),
            'activeFilters' => $this->activeFiltersCount($filters),
            'selectionKey' => 'source-categories',
        ];
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = SourceCategory::query()
            ->withoutTrashed()
            ->withCount('sourceAccounts');

        if ($filters['q'] !== '') {
            $search = $filters['q'];

            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        match ($filters['filter']) {
            'with-sources' => $query->having('source_accounts_count', '>', 0),
            'without-sources' => $query->having('source_accounts_count', '=', 0),
            default => null,
        };

        $sortColumn = match ($filters['sort']) {
            'slug' => 'slug',
            'sources' => 'source_accounts_count',
            'created_at' => 'created_at',
            default => 'name',
        };

        $query->orderBy($sortColumn, $filters['dir']);
        $query->orderBy('id', 'desc');

        return $query
            ->paginate($filters['per_page'])
            ->withQueryString();
    }

    public function filterOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'Tüm durumlar'],
            ['value' => 'with-sources', 'label' => 'Kaynağı olanlar'],
            ['value' => 'without-sources', 'label' => 'Kaynağı olmayanlar'],
        ];
    }

    public function pageSizeOptions(): array
    {
        return [15, 25, 50, 100];
    }

    public function sortColumns(array $filters): array
    {
        $columns = [
            ['label' => 'Ad', 'key' => 'name'],
            ['label' => 'Slug', 'key' => 'slug'],
            ['label' => 'Kaynak', 'key' => 'sources'],
            ['label' => 'Oluşturulma', 'key' => 'created_at'],
        ];

        return collect($columns)->map(function (array $column) use ($filters) {
            $active = $filters['sort'] === $column['key'];
            $nextDir = $active && $filters['dir'] === 'asc' ? 'desc' : 'asc';

            return [
                'label' => $column['label'],
                'key' => $column['key'],
                'class' => $active ? ('sorted-' . $filters['dir']) : '',
                'url' => route('admin.source-categories.index', array_filter([
                    'q' => $filters['q'] !== '' ? $filters['q'] : null,
                    'filter' => $filters['filter'] !== 'all' ? $filters['filter'] : null,
                    'per_page' => $filters['per_page'] !== self::DEFAULT_PER_PAGE ? $filters['per_page'] : null,
                    'sort' => $column['key'],
                    'dir' => $nextDir,
                ], static fn ($value) => $value !== null)),
            ];
        })->all();
    }

    public function summary(LengthAwarePaginator $paginator): array
    {
        return [
            'start' => $paginator->firstItem() ?? 0,
            'end' => $paginator->lastItem() ?? 0,
            'total' => $paginator->total(),
        ];
    }

    public function paginationWindow(LengthAwarePaginator $paginator, array $filters): array
    {
        $current = $paginator->currentPage();
        $last = $paginator->lastPage();

        $pages = [];

        if ($last <= 7) {
            $pages = range(1, $last);
        } else {
            $pages[] = 1;

            if ($current > 4) {
                $pages[] = 'ellipsis';
            }

            $start = max(2, $current - 1);
            $end = min($last - 1, $current + 1);

            for ($page = $start; $page <= $end; $page++) {
                $pages[] = $page;
            }

            if ($current < $last - 3) {
                $pages[] = 'ellipsis';
            }

            $pages[] = $last;
        }

        return [
            'previousUrl' => $paginator->previousPageUrl(),
            'nextUrl' => $paginator->nextPageUrl(),
            'items' => collect($pages)->map(function ($page) use ($paginator, $filters) {
                if ($page === 'ellipsis') {
                    return ['ellipsis' => true, 'number' => null, 'url' => null, 'active' => false];
                }

                return [
                    'ellipsis' => false,
                    'number' => $page,
                    'url' => route('admin.source-categories.index', array_filter([
                        'q' => $filters['q'] !== '' ? $filters['q'] : null,
                        'filter' => $filters['filter'] !== 'all' ? $filters['filter'] : null,
                        'sort' => $filters['sort'] !== self::DEFAULT_SORT ? $filters['sort'] : null,
                        'dir' => $filters['dir'] !== self::DEFAULT_DIR ? $filters['dir'] : null,
                        'per_page' => $filters['per_page'] !== self::DEFAULT_PER_PAGE ? $filters['per_page'] : null,
                        'page' => $page,
                    ], static fn ($value) => $value !== null)),
                    'active' => $paginator->currentPage() === $page,
                ];
            })->all(),
        ];
    }

    public function activeFiltersCount(array $filters): int
    {
        $count = 0;

        if ($filters['q'] !== '') {
            $count++;
        }

        if ($filters['filter'] !== 'all') {
            $count++;
        }

        return $count;
    }

    private function normalizeFilters(array $filters): array
    {
        $q = trim((string) Arr::get($filters, 'q', ''));
        $filter = (string) Arr::get($filters, 'filter', 'all');
        $sort = (string) Arr::get($filters, 'sort', self::DEFAULT_SORT);
        $dir = (string) Arr::get($filters, 'dir', self::DEFAULT_DIR);
        $perPage = (int) Arr::get($filters, 'per_page', self::DEFAULT_PER_PAGE);

        if (! in_array($filter, ['all', 'with-sources', 'without-sources'], true)) {
            $filter = 'all';
        }

        if (! in_array($sort, ['name', 'slug', 'sources', 'created_at'], true)) {
            $sort = self::DEFAULT_SORT;
        }

        if (! in_array($dir, ['asc', 'desc'], true)) {
            $dir = self::DEFAULT_DIR;
        }

        if (! in_array($perPage, $this->pageSizeOptions(), true)) {
            $perPage = self::DEFAULT_PER_PAGE;
        }

        return [
            'q' => $q,
            'filter' => $filter,
            'sort' => $sort,
            'dir' => $dir,
            'per_page' => $perPage,
        ];
    }
}
