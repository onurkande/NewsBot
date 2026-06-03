<?php

namespace App\Queries\Admin;

use App\Models\Prompt;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class PromptQuery
{
    private const DEFAULT_SORT = 'created_at';
    private const DEFAULT_DIR = 'desc';
    private const DEFAULT_PER_PAGE = 15;

    public function forIndex(array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->paginate($filters);

        return [
            'prompts' => $paginator,
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
            'selectionKey' => 'prompts',
        ];
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = Prompt::query()->with('sourceCategory');

        if ($filters['q'] !== '') {
            $search = $filters['q'];
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhereHas('sourceCategory', function ($sub) use ($search) {
                        $sub->where('name', 'like', "%{$search}%");
                    });
            });
        }

        match ($filters['filter']) {
            'active' => $query->where('is_active', true),
            'inactive' => $query->where('is_active', false),
            'deleted' => $query->onlyTrashed(),
            default => null,
        };

        $sortColumn = match ($filters['sort']) {
            'name' => 'name',
            'version' => 'version',
            default => 'created_at',
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
            ['value' => 'all', 'label' => 'Tum promptlar'],
            ['value' => 'active', 'label' => 'Aktif'],
            ['value' => 'inactive', 'label' => 'Pasif'],
            ['value' => 'deleted', 'label' => 'Silinmis'],
        ];
    }

    public function pageSizeOptions(): array
    {
        return [15, 25, 50, 100];
    }

    public function sortColumns(array $filters): array
    {
        $columns = [
            ['label' => 'Tarih', 'key' => 'created_at'],
            ['label' => 'Isim', 'key' => 'name'],
            ['label' => 'Versiyon', 'key' => 'version'],
        ];

        return collect($columns)->map(function (array $column) use ($filters) {
            $active = $filters['sort'] === $column['key'];
            $nextDir = $active && $filters['dir'] === 'asc' ? 'desc' : 'asc';

            return [
                'label' => $column['label'],
                'key' => $column['key'],
                'class' => $active ? ('sorted-' . $filters['dir']) : '',
                'url' => route('admin.prompts.index', array_filter([
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

            for ($page = max(2, $current - 1); $page <= min($last - 1, $current + 1); $page++) {
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
                    'url' => route('admin.prompts.index', array_filter([
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

        if (! in_array($filter, ['all', 'active', 'inactive', 'deleted'], true)) {
            $filter = 'all';
        }

        if (! in_array($sort, ['created_at', 'name', 'version'], true)) {
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
