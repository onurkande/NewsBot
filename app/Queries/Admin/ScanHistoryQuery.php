<?php

namespace App\Queries\Admin;

use App\Models\ClusterScanHistory;
use App\Models\StoryCluster;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;

class ScanHistoryQuery
{
    private const DEFAULT_SORT = 'scanned_at';
    private const DEFAULT_DIR = 'desc';
    private const DEFAULT_PER_PAGE = 15;

    public function forIndex(array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->paginate($filters);

        return $this->payload($paginator, $filters, 'scan-histories', 'admin.scan-histories.index');
    }

    public function forCluster(StoryCluster $cluster, array $filters = []): array
    {
        $filters = $this->normalizeFilters($filters);
        $paginator = $this->paginate($filters, $cluster);

        return $this->payload($paginator, $filters, 'cluster-scan-histories-'.$cluster->id, 'admin.story-clusters.show', [
            'storyCluster' => $cluster,
        ]);
    }

    public function paginate(array $filters, ?StoryCluster $cluster = null): LengthAwarePaginator
    {
        $query = ClusterScanHistory::query()
            ->with(['storyCluster', 'sourceAccount'])
            ->latest('scanned_at');

        if ($cluster) {
            $query->where('story_cluster_id', $cluster->id);
        }

        if ($filters['q'] !== '') {
            $search = $filters['q'];

            $query->where(function ($builder) use ($search) {
                $builder->where('last_tweet_id', 'like', "%{$search}%")
                    ->orWhere('error_message', 'like', "%{$search}%")
                    ->orWhereHas('storyCluster', function ($clusterQuery) use ($search) {
                        $clusterQuery->where('title', 'like', "%{$search}%")
                            ->orWhere('id', $search);
                    })
                    ->orWhereHas('sourceAccount', function ($sourceQuery) use ($search) {
                        $sourceQuery->where('username', 'like', "%{$search}%")
                            ->orWhere('display_name', 'like', "%{$search}%");
                    });
            });
        }

        match ($filters['filter']) {
            'success' => $query->where('status', 'success')->where('has_error', false),
            'failed' => $query->where('status', 'failed'),
            'error' => $query->where('has_error', true),
            default => null,
        };

        $query->orderBy($filters['sort'], $filters['dir']);
        $query->orderBy('id', 'desc');

        return $query
            ->paginate($filters['per_page'])
            ->withQueryString();
    }

    public function filterOptions(): array
    {
        return [
            ['value' => 'all', 'label' => 'Tüm durumlar'],
            ['value' => 'success', 'label' => 'Başarılı'],
            ['value' => 'failed', 'label' => 'Başarısız'],
            ['value' => 'error', 'label' => 'Hatalı'],
        ];
    }

    public function pageSizeOptions(): array
    {
        return [15, 25, 50, 100];
    }

    public function sortColumns(array $filters, string $routeName, array $routeParams = []): array
    {
        $columns = [
            ['label' => 'Tarama Tarihi', 'key' => 'scanned_at'],
            ['label' => 'Çekilen', 'key' => 'fetched_tweet_count'],
            ['label' => 'Yeni', 'key' => 'new_tweet_count'],
            ['label' => 'Süre', 'key' => 'duration_ms'],
        ];

        return collect($columns)->map(function (array $column) use ($filters, $routeName, $routeParams) {
            $active = $filters['sort'] === $column['key'];
            $nextDir = $active && $filters['dir'] === 'desc' ? 'asc' : 'desc';

            return [
                'label' => $column['label'],
                'key' => $column['key'],
                'class' => $active ? ('sorted-' . $filters['dir']) : '',
                'url' => route($routeName, array_filter(array_merge($routeParams, [
                    'q' => $filters['q'] !== '' ? $filters['q'] : null,
                    'filter' => $filters['filter'] !== 'all' ? $filters['filter'] : null,
                    'per_page' => $filters['per_page'] !== self::DEFAULT_PER_PAGE ? $filters['per_page'] : null,
                    'sort' => $column['key'],
                    'dir' => $nextDir,
                ]), static fn ($value) => $value !== null)),
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

    public function paginationWindow(LengthAwarePaginator $paginator, array $filters, string $routeName, array $routeParams = []): array
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
            'items' => collect($pages)->map(function ($page) use ($paginator, $filters, $routeName, $routeParams) {
                if ($page === 'ellipsis') {
                    return ['ellipsis' => true, 'number' => null, 'url' => null, 'active' => false];
                }

                return [
                    'ellipsis' => false,
                    'number' => $page,
                    'url' => route($routeName, array_filter(array_merge($routeParams, [
                        'q' => $filters['q'] !== '' ? $filters['q'] : null,
                        'filter' => $filters['filter'] !== 'all' ? $filters['filter'] : null,
                        'sort' => $filters['sort'] !== self::DEFAULT_SORT ? $filters['sort'] : null,
                        'dir' => $filters['dir'] !== self::DEFAULT_DIR ? $filters['dir'] : null,
                        'per_page' => $filters['per_page'] !== self::DEFAULT_PER_PAGE ? $filters['per_page'] : null,
                        'page' => $page,
                    ]), static fn ($value) => $value !== null)),
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

    private function payload(LengthAwarePaginator $paginator, array $filters, string $selectionKey, string $routeName, array $routeParams = []): array
    {
        return [
            'histories' => $paginator,
            'search' => $filters['q'],
            'filter' => $filters['filter'],
            'sort' => $filters['sort'],
            'dir' => $filters['dir'],
            'perPage' => $filters['per_page'],
            'filterOptions' => $this->filterOptions(),
            'pageSizeOptions' => $this->pageSizeOptions(),
            'sortColumns' => $this->sortColumns($filters, $routeName, $routeParams),
            'summary' => $this->summary($paginator),
            'pagination' => $this->paginationWindow($paginator, $filters, $routeName, $routeParams),
            'activeFilters' => $this->activeFiltersCount($filters),
            'selectionKey' => $selectionKey,
        ];
    }

    private function normalizeFilters(array $filters): array
    {
        $q = trim((string) Arr::get($filters, 'q', ''));
        $filter = (string) Arr::get($filters, 'filter', 'all');
        $sort = (string) Arr::get($filters, 'sort', self::DEFAULT_SORT);
        $dir = (string) Arr::get($filters, 'dir', self::DEFAULT_DIR);
        $perPage = (int) Arr::get($filters, 'per_page', self::DEFAULT_PER_PAGE);

        if (! in_array($filter, ['all', 'success', 'failed', 'error'], true)) {
            $filter = 'all';
        }

        if (! in_array($sort, ['scanned_at', 'duration_ms', 'new_tweet_count', 'fetched_tweet_count'], true)) {
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
