@extends('admin.layouts.master')

@section('title', 'Tweet Havuzu')
@section('active', 'pool-selection')
@section('crumbs', 'Havuz | Tweet Havuzu')

@section('content')
    <x-admin.page-header
        eyebrow="Secim Sonuclari"
        title="Tweet Havuzu"
        subtitle="Son havuz secim dongusunde degerlendirilen tweetler ve puanlari."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.pool-history.index')">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Havuz Gecmisi
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    @if (! $batch)
        <x-admin.empty-state
            title="Henuz havuz secimi yapilmadi"
            description="Havuz secimi scheduler tarafindan otomatik calistiginda sonuclar burada gorunecektir."
        />
    @else
        <x-admin.card
            eyebrow="Batch: {{ $batch->batch_no }}"
            title="{{ $batch->started_at->format('Y-m-d H:i') }} | {{ $batch->candidate_count }} aday | {{ $batch->selected_count }} secilen | {{ $batch->wait_duration_minutes }} dk bekleme"
        >
            <x-slot:actions>
                <span class="badge primary">Batch: {{ $batch->batch_no }}</span>
            </x-slot:actions>

            <form method="GET" action="{{ route('admin.pool-selection.index') }}" class="data-toolbar">
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
                            placeholder="Tweet icerigi veya ID ara..."
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
                            <th style="width: 120px;">Tweet ID</th>
                            <th>Kaynak Hesap</th>
                            <th>Tweet Icerigi</th>
                            <th style="width: 100px;">Oncelik</th>
                            <th style="width: 100px;">Etkilesim</th>
                            <th style="width: 100px;">Final</th>
                            <th style="width: 80px;">Sira</th>
                            <th style="width: 100px;">Durum</th>
                        </tr>
                    </thead>

                    <tbody>
                        @php
                            $hasSelected = $selectedItems->isNotEmpty();
                            $hasNotSelected = $notSelectedItems->isNotEmpty();
                        @endphp

                        @if ($hasSelected)
                            @foreach ($selectedItems as $item)
                                <tr style="background: rgba(16, 185, 129, 0.06);">
                                    <td class="data-cell-mono" style="color: var(--success);">{{ $item->rawTweet->tweet_id }}</td>
                                    <td>
                                        <div class="data-cell-user">
                                            <div class="data-cell-user-meta">
                                                <div class="data-cell-user-name">{{ $item->rawTweet->sourceAccount?->username ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $item->rawTweet->tweet_text }}
                                        </div>
                                    </td>
                                    <td><span class="badge primary">{{ number_format($item->priority_score, 2) }}</span></td>
                                    <td><span class="badge primary">{{ number_format($item->engagement_score, 2) }}</span></td>
                                    <td><span class="badge success">{{ number_format($item->final_score, 2) }}</span></td>
                                    <td><span class="data-cell-mono">{{ $item->rank }}</span></td>
                                    <td><span class="tag t-active">Secildi</span></td>
                                </tr>
                            @endforeach
                        @endif

                        @if ($hasSelected && $hasNotSelected)
                            <tr>
                                <td colspan="8" style="padding: 0;">
                                    <div style="display: flex; align-items: center; gap: 12px; padding: 8px 16px; background: var(--bg-3); border-top: 1px dashed var(--border); border-bottom: 1px dashed var(--border);">
                                        <div style="flex: 1; height: 1px; background: var(--border);"></div>
                                        <span style="font-size: 12px; font-weight: 600; color: var(--t-muted); text-transform: uppercase; letter-spacing: 0.5px;">Baraj Cizgisi &mdash; Yukarisi Secilenler</span>
                                        <div style="flex: 1; height: 1px; background: var(--border);"></div>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        @if ($hasNotSelected)
                            @foreach ($notSelectedItems as $item)
                                <tr style="background: rgba(244, 63, 94, 0.04);">
                                    <td class="data-cell-mono" style="color: var(--danger);">{{ $item->rawTweet->tweet_id }}</td>
                                    <td>
                                        <div class="data-cell-user">
                                            <div class="data-cell-user-meta">
                                                <div class="data-cell-user-name">{{ $item->rawTweet->sourceAccount?->username ?? '—' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            {{ $item->rawTweet->tweet_text }}
                                        </div>
                                    </td>
                                    <td><span class="badge primary">{{ number_format($item->priority_score, 2) }}</span></td>
                                    <td><span class="badge primary">{{ number_format($item->engagement_score, 2) }}</span></td>
                                    <td><span class="badge primary">{{ number_format($item->final_score, 2) }}</span></td>
                                    <td><span class="data-cell-mono">{{ $item->rank }}</span></td>
                                    <td><span class="tag t-unavail">Secilmedi</span></td>
                                </tr>
                            @endforeach
                        @endif

                        @if (! $hasSelected && ! $hasNotSelected)
                            <tr>
                                <td colspan="8">
                                    <x-admin.empty-state
                                        title="Bu batch icin kayit bulunamadi"
                                        description="Filtreleri temizleyerek tum sonuclari gorebilirsiniz."
                                    />
                                </td>
                            </tr>
                        @endif
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

                    <form method="GET" action="{{ route('admin.pool-selection.index') }}">
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
    @endif
@endsection
