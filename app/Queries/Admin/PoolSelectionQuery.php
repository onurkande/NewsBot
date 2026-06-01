<?php

namespace App\Queries\Admin;

use App\Models\PoolBatch;
use App\Models\PoolBatchItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class PoolSelectionQuery
{
    private const DEFAULT_SORT = 'final_score';
    private const DEFAULT_DIR = 'desc';
    private const DEFAULT_PER_PAGE = 15;

    public function forIndex(array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);

        $latestBatch = PoolBatch::query()->latest('started_at')->first();

        if (! $latestBatch) {
            return [
                'batch' => null,
                'items' => null,
                'selectedItems' => collect(),
                'notSelectedItems' => collect(),
                'search' => $filters['q'],
                'filter' => $filters['filter'],
                'sort' => $filters['sort'],
                'dir' => $filters['dir'],
                'perPage' => $filters['per_page'],
                'filterOptions' => $this->filterOptions(),
                'pageSizeOptions' => $this->pageSizeOptions(),
                'sortColumns' => [],
                'summary' => ['start' => 0, 'end' => 0, 'total' => 0],
                'pagination' => ['previousUrl' => null, 'nextUrl' => null, 'items' => []],
                'activeFilters' => 0,
                'selectionKey' => 'pool-selection',
            ];
        }

        $paginator = $this->paginate($filters, $latestBatch);

        $selectedItems = collect($paginator->items())->filter(fn ($item) => $item->is_selected);
        $notSelectedItems = collect($paginator->items())->filter(fn ($item) => ! $item->is_selected);

        return [
            'batch' => $latestBatch,
            'items' => $paginator,
            'selectedItems' => $selectedItems,
            'notSelectedItems' => $notSelectedItems,
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
            'selectionKey' => 'pool-selection',
        ];
    }

    public function paginate(array $filters, PoolBatch $batch): LengthAwarePaginator
    {
        $query = PoolBatchItem::query()
            ->with(['rawTweet.sourceAccount'])
            ->where('pool_batch_id', $batch->id);

        if ($filters['q'] !== '') {
            $search = $filters['q'];
            $query->whereHas('rawTweet', function ($builder) use ($search) {
                $builder->where('tweet_text', 'like', "%{$search}%")
                    ->orWhere('tweet_id', 'like', "%{$search}%");
            });
        }

        match ($filters['filter']) {
            'selected' => $query->where('is_selected', true),
            'not_selected' => $query->where('is_selected', false),
            default => null,
        };

        $sortColumn = match ($filters['sort']) {
            'priority_score' => 'priority_score',
            'engagement_score' => 'engagement_score',
            'rank' => 'rank',
            default => 'final_score',
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
            ['value' => 'all', 'label' => 'Tum tweetler'],
            ['value' => 'selected', 'label' => 'Secilenler'],
            ['value' => 'not_selected', 'label' => 'Secilmeyenler'],
        ];
    }

    public function pageSizeOptions(): array
    {
        return [15, 25, 50, 100];
    }

    public function sortColumns(array $filters): array
    {
        $columns = [
            ['label' => 'Final Puan', 'key' => 'final_score'],
            ['label' => 'Oncelik Puan', 'key' => 'priority_score'],
            ['label' => 'Etkilesim Puan', 'key' => 'engagement_score'],
            ['label' => 'Sira', 'key' => 'rank'],
        ];

        return collect($columns)->map(function (array $column) use ($filters) {
            $active = $filters['sort'] === $column['key'];
            $nextDir = $active && $filters['dir'] === 'asc' ? 'desc' : 'asc';

            return [
                'label' => $column['label'],
                'key' => $column['key'],
                'class' => $active ? ('sorted-' . $filters['dir']) : '',
                'url' => route('admin.pool-selection.index', array_filter([
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
                    'url' => route('admin.pool-selection.index', array_filter([
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

        if (! in_array($filter, ['all', 'selected', 'not_selected'], true)) {
            $filter = 'all';
        }

        if (! in_array($sort, ['final_score', 'priority_score', 'engagement_score', 'rank'], true)) {
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
