<?php

namespace App\Queries\Admin;

use App\Models\SourceAccount;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SourceAccountQuery
{
    private const DEFAULT_SORT = 'username';
    private const DEFAULT_DIR = 'asc';
    private const DEFAULT_PER_PAGE = 15;

    public function forIndex(array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->paginate($filters);

        return [
            'accounts' => $paginator,
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
            'selectionKey' => 'source-accounts',
        ];
    }

    public function paginate(array $filters): LengthAwarePaginator
    {
        $query = SourceAccount::query()
            ->with('category');

        if ($filters['q'] !== '') {
            $search = $filters['q'];

            $query->where(function ($builder) use ($search) {
                $builder->where('username', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        match ($filters['filter']) {
            'active' => $query->where('is_active', true),
            'passive' => $query->where('is_active', false),
            default => null,
        };

        $sortColumn = match ($filters['sort']) {
            'username' => 'username',
            'trust_score' => 'trust_score',
            'priority_score' => 'priority_score',
            'check_interval' => 'check_interval_minutes',
            'last_checked' => 'last_checked_at',
            default => 'username',
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
            ['value' => 'active', 'label' => 'Aktif'],
            ['value' => 'passive', 'label' => 'Pasif'],
        ];
    }

    public function pageSizeOptions(): array
    {
        return [15, 25, 50, 100];
    }

    public function sortColumns(array $filters): array
    {
        $columns = [
            ['label' => 'Hesap', 'key' => 'username'],
            ['label' => 'Güven', 'key' => 'trust_score'],
            ['label' => 'Öncelik', 'key' => 'priority_score'],
            ['label' => 'Aralık', 'key' => 'check_interval'],
            ['label' => 'Son Kontrol', 'key' => 'last_checked'],
        ];

        return collect($columns)->map(function (array $column) use ($filters) {
            $active = $filters['sort'] === $column['key'];
            $nextDir = $active && $filters['dir'] === 'asc' ? 'desc' : 'asc';

            return [
                'label' => $column['label'],
                'key' => $column['key'],
                'class' => $active ? ('sorted-' . $filters['dir']) : '',
                'url' => route('admin.source-accounts.index', array_filter([
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
                    'url' => route('admin.source-accounts.index', array_filter([
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
        return [
            'q' => trim((string) ($filters['q'] ?? '')),
            'filter' => $filters['filter'] ?? 'all',
            'sort' => $filters['sort'] ?? self::DEFAULT_SORT,
            'dir' => strtolower((string) ($filters['dir'] ?? self::DEFAULT_DIR)),
            'per_page' => (int) ($filters['per_page'] ?? self::DEFAULT_PER_PAGE),
        ];
    }
}
