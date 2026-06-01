<?php

namespace App\Queries\Admin\Twscrape;

use App\Models\TwscrapeLog;

class TwscrapeLogQuery
{
    public function forIndex(array $filters = []): array
    {
        $query = TwscrapeLog::query();

        if (!empty($filters['q'])) {
            $query->where(function($q) use ($filters) {
                $q->where('command', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('output', 'like', '%' . $filters['q'] . '%')
                  ->orWhere('username', 'like', '%' . $filters['q'] . '%');
            });
        }

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        $sort = $filters['sort'] ?? 'created_at';
        $dir = $filters['dir'] ?? 'desc';
        
        $query->orderBy($sort, $dir);

        $perPage = $filters['per_page'] ?? 15;
        $paginator = $query->paginate($perPage)->withQueryString();

        return [
            'logs' => $paginator,
            'search' => $filters['q'] ?? '',
            'type' => $filters['type'] ?? 'all',
            'sort' => $sort,
            'dir' => $dir,
            'perPage' => $perPage,
        ];
    }
}
