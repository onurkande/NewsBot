@extends('admin.layouts.master')

@section('title', 'Havuz Gecmisi')
@section('active', 'pool-history')
@section('crumbs', 'Havuz | Havuz Gecmisi')

@section('content')
    <x-admin.page-header
        eyebrow="Gecmis"
        title="Havuz Gecmisi"
        subtitle="Gerceklesen tum havuz secim dongulerinin kayitlari."
    />

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Secim Donguleri">
        <form method="GET" action="{{ route('admin.pool-history.index') }}" class="data-toolbar">
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
                        placeholder="Batch no ara..."
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
            <table class="data-table" style="margin: 0 22px; min-width: 1000px;">
                <thead>
                    <tr>
                        <th>Batch No</th>
                        <th class="{{ $sortColumns[0]['class'] }}">
                            <a href="{{ $sortColumns[0]['url'] }}">
                                {{ $sortColumns[0]['label'] }}
                                <span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                            </a>
                        </th>
                        <th>Aralik</th>
                        @foreach (array_slice($sortColumns, 1) as $column)
                            <th class="{{ $column['class'] }}">
                                <a href="{{ $column['url'] }}">
                                    {{ $column['label'] }}
                                    <span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                                </a>
                            </th>
                        @endforeach
                        <th>Sonraki Calisma</th>
                        <th>Durum</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($batches as $batch)
                        <tr class="data-row">
                            <td class="data-cell-mono">{{ $batch->batch_no }}</td>
                            <td>{{ $batch->started_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $batch->tweet_window_minutes }} dk</td>
                            <td>{{ $batch->candidate_count }}</td>
                            <td>{{ $batch->selected_count }}</td>
                            <td>{{ $batch->wait_duration_minutes }} dk</td>
                            <td>{{ $batch->next_run_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="tag {{ $batch->status === 'completed' ? 't-active' : 't-unavail' }}">
                                    {{ $batch->status === 'completed' ? 'Tamamlandi' : 'Basarisiz' }}
                                </span>
                            </td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <a class="btn--icon" href="{{ route('admin.pool-history.show', $batch) }}" aria-label="Detay">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-admin.empty-state
                                    title="Henuz havuz gecmisi yok"
                                    description="Havuz secimi calistiginda kayitlar burada listelenecektir."
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
                    <strong style="color: var(--t-base);">{{ $summary['start'] }}&ndash;{{ $summary['end'] }}</strong>
                    of
                    <strong style="color: var(--t-base);">{{ $summary['total'] }}</strong>
                </span>

                <form method="GET" action="{{ route('admin.pool-history.index') }}">
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
                        <button class="pager-btn" disabled>&hellip;</button>
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
@endsection
