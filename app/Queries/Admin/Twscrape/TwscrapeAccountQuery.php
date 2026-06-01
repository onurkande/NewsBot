<?php

namespace App\Queries\Admin\Twscrape;

use App\Models\TwscrapeAccount;

class TwscrapeAccountQuery
{
    public function forIndex(array $filters = []): array
    {
        $query = TwscrapeAccount::query();

        if (!empty($filters['q'])) {
            $query->where('username', 'like', '%' . $filters['q'] . '%');
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'passive') {
                $query->where('is_active', false);
            }
        }

        $sort = $filters['sort'] ?? 'username';
        $dir = $filters['dir'] ?? 'asc';
        
        $query->orderBy($sort, $dir);

        $perPage = $filters['per_page'] ?? 15;
        $paginator = $query->paginate($perPage)->withQueryString();

        return [
            'accounts' => $paginator,
            'search' => $filters['q'] ?? '',
            'status' => $filters['status'] ?? 'all',
            'sort' => $sort,
            'dir' => $dir,
            'perPage' => $perPage,
        ];
    }
}
