<x-admin.card eyebrow="Liste" title="{{ $tableTitle ?? 'Tarama kayıtları' }}">
    <x-slot:actions>
        <span class="badge primary" data-selected-count data-selection-key="{{ $selectionKey }}">0 seçili</span>
    </x-slot:actions>

    <form method="GET" action="{{ $historyAction }}" class="data-toolbar">
        <div class="data-toolbar-left">
            <div class="input-icon" style="flex: 1; max-width: 320px;">
                <span class="ico">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                </span>
                <input
                    class="input"
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Cluster, kaynak, tweet ID veya hata ara..."
                >
            </div>

            <button class="btn btn--ghost" type="submit">
                <svg viewBox="0 0 24 24"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/></svg>
                Filtrele
                <span class="badge primary" style="margin-left: 4px;">{{ $activeFilters }}</span>
            </button>
        </div>

        <div class="data-toolbar-right">
            <select class="select" name="filter" onchange="this.form.submit()">
                @foreach ($filterOptions as $option)
                    <option value="{{ $option['value'] }}" @selected($filter === $option['value'])>
                        {{ $option['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="sort" value="{{ $sort }}">
        <input type="hidden" name="dir" value="{{ $dir }}">
        <input type="hidden" name="per_page" value="{{ $perPage }}">
    </form>

    <div style="overflow-x: auto; margin: 0 -22px;">
        <table
            class="data-table"
            data-selection-table
            data-selection-key="{{ $selectionKey }}"
            style="margin: 0 22px; min-width: {{ ! empty($showClusterColumns) ? '1680px' : '1420px' }};"
        >
            <thead>
                <tr>
                    <th style="width: 32px;">
                        <label class="check">
                            <input type="checkbox" data-master-checkbox>
                            <span class="box"></span>
                        </label>
                    </th>
                    <th>ID</th>
                    @if (! empty($showClusterColumns))
                        <th>Cluster ID</th>
                        <th>Cluster Adı</th>
                    @endif
                    <th>Kaynak Hesap</th>
                    @foreach ($sortColumns as $column)
                        <th class="{{ $column['class'] }}">
                            <a href="{{ $column['url'] }}">
                                {{ $column['label'] }}
                                <span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                            </a>
                        </th>
                    @endforeach
                    <th>Önceki Süre</th>
                    <th>İşlenen</th>
                    <th>Atlanan</th>
                    <th>Son Tweet ID</th>
                    <th>Durum</th>
                    <th>Hata</th>
                    <th>Sonraki Tarama</th>
                    <th>Oluşturulma</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($histories as $history)
                    <tr class="data-row" data-row-id="{{ $history->id }}">
                        <td>
                            <label class="check">
                                <input type="checkbox" data-row-checkbox data-row-id="{{ $history->id }}">
                                <span class="box"></span>
                            </label>
                        </td>

                        <td class="data-cell-mono">#{{ $history->id }}</td>

                        @if (! empty($showClusterColumns))
                            <td class="data-cell-mono">
                                @if ($history->storyCluster)
                                    <a href="{{ route('admin.story-clusters.show', $history->storyCluster) }}">#{{ $history->storyCluster->id }}</a>
                                @else
                                    -
                                @endif
                            </td>
                            <td style="white-space: normal; min-width: 260px;">
                                {{ $history->storyCluster?->title ? Str::limit($history->storyCluster->title, 90) : '-' }}
                            </td>
                        @endif

                        <td>
                            @if ($history->sourceAccount)
                                <div class="data-cell-user-name">{{ $history->sourceAccount->display_name ?: '@'.$history->sourceAccount->username }}</div>
                                <div class="data-cell-user-email">{{ '@'.$history->sourceAccount->username }}</div>
                            @else
                                -
                            @endif
                        </td>

                        <td class="data-cell-mono">{{ $history->scanned_at?->format('Y-m-d H:i:s') ?: '-' }}</td>
                        <td><span class="badge info">{{ $history->fetched_tweet_count }}</span></td>
                        <td><span class="badge success">{{ $history->new_tweet_count }}</span></td>
                        <td class="data-cell-mono">{{ $history->duration_ms !== null ? $history->duration_ms.' ms' : '-' }}</td>
                        <td class="data-cell-mono">{{ $history->previous_scan_elapsed_minutes !== null ? $history->previous_scan_elapsed_minutes.' dk' : '-' }}</td>
                        <td><span class="badge primary">{{ $history->processed_tweet_count }}</span></td>
                        <td><span class="badge info">{{ $history->skipped_tweet_count }}</span></td>
                        <td class="data-cell-mono">{{ $history->last_tweet_id ?: '-' }}</td>
                        <td>
                            <span class="tag {{ $history->status === 'success' ? 't-active' : 't-unavail' }}">
                                {{ $history->status }}
                            </span>
                        </td>
                        <td>
                            @if ($history->has_error)
                                <span class="badge danger">{{ Str::limit($history->error_message ?: 'Hata', 40) }}</span>
                            @else
                                <span class="badge success">Yok</span>
                            @endif
                        </td>
                        <td>
                            @if ($history->next_check_at)
                                <div class="data-cell-mono">{{ $history->next_check_interval_minutes ?: '-' }} dk</div>
                                <div class="data-cell-user-email">{{ $history->next_check_at->format('Y-m-d H:i') }}</div>
                            @else
                                -
                            @endif
                        </td>
                        <td class="data-cell-mono">{{ $history->created_at?->format('Y-m-d H:i') ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ! empty($showClusterColumns) ? 18 : 16 }}">
                            <x-admin.empty-state
                                title="Henüz tarama kaydı yok"
                                description="Kaynak taramaları tamamlandığında kayıtlar burada listelenecektir."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="data-foot">
        <div class="data-foot-info">
            <span>
                Showing
                <strong style="color: var(--t-base);">{{ $summary['start'] }}-{{ $summary['end'] }}</strong>
                of
                <strong style="color: var(--t-base);">{{ $summary['total'] }}</strong>
            </span>

            <form method="GET" action="{{ $historyAction }}">
                <select class="select" name="per_page" onchange="this.form.submit()">
                    @foreach ($pageSizeOptions as $option)
                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }} per page</option>
                    @endforeach
                </select>

                <input type="hidden" name="q" value="{{ $search }}">
                <input type="hidden" name="filter" value="{{ $filter }}">
                <input type="hidden" name="sort" value="{{ $sort }}">
                <input type="hidden" name="dir" value="{{ $dir }}">
            </form>
        </div>

        <div class="pager" aria-label="Pagination">
            @if ($pagination['previousUrl'])
                <a class="pager-btn" href="{{ $pagination['previousUrl'] }}" aria-label="Previous">
                    <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                </a>
            @else
                <button class="pager-btn" disabled aria-label="Previous">
                    <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                </button>
            @endif

            @foreach ($pagination['items'] as $item)
                @if ($item['ellipsis'])
                    <button class="pager-btn" disabled>...</button>
                @else
                    <a class="pager-btn {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['url'] }}">{{ $item['number'] }}</a>
                @endif
            @endforeach

            @if ($pagination['nextUrl'])
                <a class="pager-btn" href="{{ $pagination['nextUrl'] }}" aria-label="Next">
                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </a>
            @else
                <button class="pager-btn" disabled aria-label="Next">
                    <svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
                </button>
            @endif
        </div>
    </div>
</x-admin.card>
