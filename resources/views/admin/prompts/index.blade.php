@extends('admin.layouts.master')

@section('title', 'Prompt Yonetimi')
@section('active', 'prompts')
@section('crumbs', 'AI Yonetimi | Prompt Yonetimi')

@section('content')
    <x-admin.page-header
        eyebrow="Yonetim"
        title="Prompt Yonetimi"
        subtitle="AI icerik uretimi icin kullanilacak prompt sablonlarini buradan yonetin."
    >
        <x-slot:actions>
            <x-admin.button variant="primary" :href="route('admin.prompts.create')">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                Yeni Prompt
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Promptlar">
        <form method="GET" action="{{ route('admin.prompts.index') }}" class="data-toolbar">
            <div class="data-toolbar-left">
                <div class="input-icon" style="flex: 1; max-width: 320px;">
                    <span class="ico">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Prompt adi veya kategori ara...">
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
                        <th style="width: 40px;">
                            <input type="checkbox" class="select-all" data-table="prompts-table">
                        </th>
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

                <tbody id="prompts-table">
                    @forelse ($prompts as $prompt)
                        <tr class="data-row">
                            <td>
                                <input type="checkbox" class="select-item" form="bulk-destroy-form" name="selected[]" value="{{ $prompt->id }}">
                            </td>
                            <td class="data-cell-mono">{{ $prompt->name }}</td>
                            <td>
                                <span class="badge primary">{{ $prompt->sourceCategory?->name ?: 'Global' }}</span>
                            </td>
                            <td>v{{ $prompt->version }}</td>
                            <td>
                                @if ($prompt->is_active)
                                    <span class="tag t-active">Aktif</span>
                                @else
                                    <span class="tag t-unavail">Pasif</span>
                                @endif
                            </td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    @if ($prompt->is_active)
                                        <form method="POST" action="{{ route('admin.prompts.deactivate', $prompt) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn--icon" title="Deaktif Et" aria-label="Deaktif Et">
                                                <svg viewBox="0 0 24 24"><line x1="1" y1="1" x2="23" y2="23"/><path d="M21 21H3V3"/></svg>
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.prompts.activate', $prompt) }}" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn--icon" title="Aktif Et" aria-label="Aktif Et" style="color: var(--success)">
                                                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                            </button>
                                        </form>
                                    @endif
                                    <a class="btn--icon" href="{{ route('admin.prompts.edit', $prompt) }}" aria-label="Duzenle">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.prompts.destroy', $prompt) }}" style="display: inline;" onsubmit="return confirm('Bu promptu silmek istediginize emin misiniz?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn--icon" aria-label="Sil" style="color: var(--danger)">
                                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-admin.empty-state title="Henuz prompt yok" description="Yeni prompt olusturarak AI uretimlerini ozellestirebilirsiniz." />
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

                <form method="GET" action="{{ route('admin.prompts.index') }}">
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

                <form id="bulk-destroy-form" method="POST" action="{{ route('admin.prompts.bulk-destroy') }}" onsubmit="return confirm('Secili promptlari silmek istediginize emin misiniz?')" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn--ghost" style="color: var(--danger);">Secili Sil</button>
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
