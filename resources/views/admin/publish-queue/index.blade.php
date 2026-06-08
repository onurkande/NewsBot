@extends('admin.layouts.master')

@section('title', 'Yayin Kuyrugu')
@section('active', 'publish-queue')
@section('crumbs', 'Yayin Yonetimi | Kuyruk')

@section('content')
    <x-admin.page-header
        eyebrow="Kuyruk"
        title="Yayin Kuyrugu"
        subtitle="Yayin bekleyen kayitlari ve durumlarini gorun."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.publish-history.index')">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Yayin Gecmisi
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Kuyruk Kayitlari">
        <form method="GET" action="{{ route('admin.publish-queue.index') }}" class="data-toolbar">
            <div class="data-toolbar-left">
                <div class="input-icon" style="flex: 1; max-width: 320px;">
                    <span class="ico">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Icerik ara...">
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
            <table class="data-table" style="margin: 0 22px; min-width: 1100px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>AI Generation</th>
                        <th>Baslik</th>
                        <th>Durum</th>
                        <th>Hedef Hesap</th>
                        <th>Eklenme Tarihi</th>
                        <th>Son Islem</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="data-row">
                            <td class="data-cell-mono">{{ $item->id }}</td>
                            <td class="data-cell-mono">{{ $item->ai_generation_id }}</td>
                            <td>
                                @if ($item->aiGeneration)
                                    <div style="max-width: 220px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 13px;">
                                        {{ $item->aiGeneration->title ?? Str::limit($item->aiGeneration->generated_news, 60) }}
                                    </div>
                                @else
                                    <span style="color: var(--t-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $statusTag = match($item->status) {
                                        'pending' => 't-warning',
                                        'processing' => 't-info',
                                        'published' => 't-active',
                                        'failed' => 't-danger',
                                        default => 't-unavail',
                                    };
                                    $statusLabel = match($item->status) {
                                        'pending' => 'Bekliyor',
                                        'processing' => 'Isleniyor',
                                        'published' => 'Yayinlandi',
                                        'failed' => 'Basarisiz',
                                        default => $item->status,
                                    };
                                @endphp
                                <span class="tag {{ $statusTag }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                @if ($item->publishAccount)
                                    <span style="font-weight: 600;">{{ $item->publishAccount->username }}</span>
                                @else
                                    <span style="color: var(--t-muted);">—</span>
                                @endif
                            </td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                @if ($item->completed_at)
                                    {{ $item->completed_at->format('Y-m-d H:i') }}
                                @elseif ($item->started_at)
                                    {{ $item->started_at->format('Y-m-d H:i') }}
                                @else
                                    <span style="color: var(--t-muted);">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <a class="btn--icon" href="{{ route('admin.publish-queue.show', $item) }}" aria-label="Detay">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    @if ($item->status === 'failed')
                                        <form method="POST" action="{{ route('admin.publish-queue.retry', $item) }}" style="display:inline">
                                            @csrf
                                            <button class="btn--icon" type="submit" aria-label="Tekrar Dene" title="Tekrar Dene">
                                                <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state title="Kuyruk bos" description="Yayin bekleyen kayit bulunmuyor." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-foot">
            <div class="data-foot-info">
                <span>
                    Showing <strong style="color: var(--t-base);">{{ $summary['start'] }}&ndash;{{ $summary['end'] }}</strong> of <strong style="color: var(--t-base);">{{ $summary['total'] }}</strong>
                </span>
                <form method="GET" action="{{ route('admin.publish-queue.index') }}">
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
                    <a class="pager-btn" href="{{ $pagination['previousUrl'] }}" aria-label="Previous"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></a>
                @else
                    <button class="pager-btn" disabled aria-label="Previous"><svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg></button>
                @endif
                @foreach ($pagination['items'] as $item)
                    @if ($item['ellipsis'])
                        <button class="pager-btn" disabled>&hellip;</button>
                    @else
                        <a class="pager-btn {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['url'] }}">{{ $item['number'] }}</a>
                    @endif
                @endforeach
                @if ($pagination['nextUrl'])
                    <a class="pager-btn" href="{{ $pagination['nextUrl'] }}" aria-label="Next"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></a>
                @else
                    <button class="pager-btn" disabled aria-label="Next"><svg viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg></button>
                @endif
            </div>
        </div>
    </x-admin.card>
@endsection
