@extends('admin.layouts.master')

@section('title', 'AI Kuyrugu')
@section('active', 'ai-queue')
@section('crumbs', 'AI Yonetimi | AI Kuyrugu')

@section('content')
    <x-admin.page-header
        eyebrow="Kuyruk"
        title="AI Kuyrugu"
        subtitle="Havuz secimi sonrasi AI uretimine gonderilmeyi bekleyen batchler."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.ai-generations.index')">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                AI Uretimleri
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Kuyruk Kayitlari">
        <form method="GET" action="{{ route('admin.ai-queue.index') }}" class="data-toolbar">
            <div class="data-toolbar-left">
                <div class="input-icon" style="flex: 1; max-width: 320px;">
                    <span class="ico">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Batch no ara...">
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
            <table class="data-table" style="margin: 0 22px; min-width: 900px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th class="{{ $sortColumns[0]['class'] }}">
                            <a href="{{ $sortColumns[0]['url'] }}">{{ $sortColumns[0]['label'] }}<span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span></a>
                        </th>
                        @foreach (array_slice($sortColumns, 1) as $column)
                            <th class="{{ $column['class'] }}">
                                <a href="{{ $column['url'] }}">{{ $column['label'] }}<span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span></a>
                            </th>
                        @endforeach
                        <th>Durum</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($queues as $queue)
                        <tr class="data-row">
                            <td class="data-cell-mono">{{ $queue->id }}</td>
                            <td>{{ $queue->created_at->format('Y-m-d H:i') }}</td>
                            <td class="data-cell-mono">{{ $queue->batch_no }}</td>
                            <td>{{ $queue->tweet_count }}</td>
                            <td><span class="badge primary">{{ number_format($queue->story_score, 2) }}</span></td>
                            <td>
                                @php
                                    $statusTag = match($queue->status) {
                                        'pending' => 't-unavail',
                                        'processing' => 't-info',
                                        'completed' => 't-active',
                                        'failed' => 't-danger',
                                        default => 't-unavail',
                                    };
                                    $statusLabel = match($queue->status) {
                                        'pending' => 'Bekliyor',
                                        'processing' => 'Isleniyor',
                                        'completed' => 'Tamamlandi',
                                        'failed' => 'Basarisiz',
                                        default => $queue->status,
                                    };
                                @endphp
                                <span class="tag {{ $statusTag }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <a class="btn--icon" href="{{ route('admin.ai-queue.show', $queue) }}" aria-label="Detay">
                                        <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state title="Henuz kuyruk kaydi yok" description="Havuz secimi calisip batch olustukca AI kuyrugu otomatik olarak dolacaktir." />
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

                <form method="GET" action="{{ route('admin.ai-queue.index') }}">
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
