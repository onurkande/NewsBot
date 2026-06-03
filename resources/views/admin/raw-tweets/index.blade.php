@extends('admin.layouts.master')

@section('title', 'Ham Tweetler')
@section('active', $selectionKey)
@section('crumbs', 'Haber Toplama | Ham Tweetler')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Çıktısı"
        title="Ham tweetler"
        subtitle="Sistem tarafından X üzerinden çekilen ancak henüz işlenmemiş tweet listesi."
    >
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Tweetler">
        <x-slot:actions>
            <span class="badge primary" data-selected-count data-selection-key="{{ $selectionKey }}">0 seçili</span>
        </x-slot:actions>

        <form method="GET" action="{{ route('admin.raw-tweets.index') }}" class="data-toolbar">
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
                        placeholder="Tweet metni veya ID ara..."
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

                <button class="btn btn--ghost btn--icon" type="button" aria-label="Columns">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="18"/><rect x="14" y="3" width="7" height="11"/></svg>
                </button>
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
                style="margin: 0 22px; min-width: 1000px;"
            >
                <thead>
                    <tr>
                        <th style="width: 32px;">
                            <label class="check">
                                <input type="checkbox" data-master-checkbox>
                                <span class="box"></span>
                            </label>
                        </th>
                        <th>Tweet</th>
                        <th>Kaynak</th>
                        <th>Story</th>
                        @foreach ($sortColumns as $column)
                            <th class="{{ $column['class'] }}">
                                <a href="{{ $column['url'] }}">
                                    {{ $column['label'] }}
                                    <span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                                </a>
                            </th>
                        @endforeach
                        <th>Durum</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tweets as $tweet)
                        <tr class="data-row" data-row-id="{{ $tweet->id }}">
                            <td>
                                <label class="check">
                                    <input type="checkbox" data-row-checkbox data-row-id="{{ $tweet->id }}">
                                    <span class="box"></span>
                                </label>
                            </td>

                            <td style="white-space: normal; min-width: 360px">
                                <div class="data-cell-user-name">{{ Str::limit($tweet->tweet_text, 140) }}</div>
                                <div class="data-cell-mono">{{ $tweet->tweet_id }}</div>
                            </td>

                            <td>
                                <div class="data-cell-user">
                                    <div class="data-cell-user-meta">
                                        <div class="data-cell-user-name">{{ $tweet->sourceAccount->display_name ?: '@'.$tweet->sourceAccount->username }}</div>
                                        <div class="data-cell-user-email">{{ '@'.$tweet->sourceAccount->username }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>{{ $tweet->storyClusterItem?->storyCluster?->title ? Str::limit($tweet->storyClusterItem->storyCluster->title, 60) : '-' }}</td>

                            <td class="data-cell-mono">{{ $tweet->tweeted_at?->format('Y-m-d H:i') ?: '-' }}</td>

                            <td><span class="badge info">L {{ $tweet->like_count }} / RT {{ $tweet->retweet_count }}</span></td>

                            <td>
                                <span class="tag {{ $tweet->is_processed ? 't-active' : 't-old' }}">
                                    {{ $tweet->is_processed ? 'İşlendi' : 'Bekliyor' }}
                                </span>
                            </td>

                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <a class="btn--icon" href="{{ route('admin.raw-tweets.show', $tweet) }}" aria-label="Detay">
                                        <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state
                                    title="Henüz ham tweet yok"
                                    description="Twscrape üzerinden veri geldiğinde burada listelenecektir."
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
                    <strong style="color: var(--t-base);">{{ $summary['start'] }}–{{ $summary['end'] }}</strong>
                    of
                    <strong style="color: var(--t-base);">{{ $summary['total'] }}</strong>
                </span>

                <form method="GET" action="{{ route('admin.raw-tweets.index') }}">
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
                        <button class="pager-btn" disabled>…</button>
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
