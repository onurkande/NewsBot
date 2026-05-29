@extends('admin.layouts.master')

@section('title', 'Kaynak Kategorileri')
@section('active', 'source-categories')
@section('crumbs', 'Haber Toplama | Kaynak Kategorileri')

@section('content')
    <section class="hero">
        <div class="hero-text">
            <span class="eyebrow">Haber Toplama</span>
            <h1 class="hero-title">Kaynak kategorileri</h1>
            <p class="hero-sub">X kaynaklarını konu başlıklarına ayırarak tarama, skorlama ve story gruplama adımlarını daha okunur hale getirin.</p>
        </div>

        <div class="hero-actions">
            <a class="btn btn--primary" href="{{ route('admin.source-categories.create') }}">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" /></svg>
                Yeni kategori
            </a>
        </div>
    </section>

    @include('admin.partials.flash')

    <section class="grid">
        <section class="col-12 card" data-selection-key="source-categories">
            <div class="card-head">
                <div class="card-title-wrap">
                    <span class="eyebrow">Liste</span>
                    <h2 class="card-title">Kategoriler</h2>
                </div>

                <div class="card-head-actions" style="display: flex; align-items: center; gap: 12px;">
                    <span class="badge primary" data-selected-count>0 seçili</span>
                    <button type="button" class="btn btn--danger" data-bulk-delete-trigger disabled>
                        <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M19 6l-1 14H6L5 6" /></svg>
                        Toplu sil
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.source-categories.index') }}" class="data-toolbar">
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
                            placeholder="Kategori, slug veya açıklama ara..."
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
            </form>

            <div style="overflow-x: auto; margin: 0 -22px;">
                <table class="data-table" data-selection-table data-selection-key="source-categories" style="margin: 0 22px; min-width: 900px;">
                    <thead>
                        <tr>
                            <th style="width: 32px;">
                                <label class="check">
                                    <input type="checkbox" data-master-checkbox>
                                    <span class="box"></span>
                                </label>
                            </th>

                            @foreach ($sortColumns as $column)
                                <th class="{{ $column['class'] }}">
                                    <a href="{{ $column['url'] }}">
                                        {{ $column['label'] }}
                                        <span class="sort"><svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg></span>
                                    </a>
                                </th>
                            @endforeach

                            <th style="text-align: right">İşlemler</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="data-row">
                                <td>
                                    <label class="check">
                                        <input type="checkbox" data-row-checkbox data-row-id="{{ $category->id }}">
                                        <span class="box"></span>
                                    </label>
                                </td>

                                <td>
                                    <div class="data-cell-user">
                                        <div class="av {{ $category->avatar_class }}">{{ $category->display_initials }}</div>
                                        <div class="data-cell-user-meta">
                                            <div class="data-cell-user-name">{{ $category->name }}</div>
                                            <div class="data-cell-user-email">{{ $category->slug }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td><span class="data-cell-mono">{{ $category->slug }}</span></td>

                                <td><span class="badge primary">{{ $category->sources_label }}</span></td>

                                <td>{{ $category->description_text }}</td>

                                <td>
                                    <div class="data-cell-actions">
                                        <a class="btn--icon" href="{{ route('admin.source-categories.edit', $category) }}" aria-label="Düzenle">
                                            <svg viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z"/></svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.source-categories.destroy', $category) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn--icon" type="submit" aria-label="Sil">
                                                <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M19 6l-1 14H6L5 6" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Henüz kategori yok.</td>
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

                    <form method="GET" action="{{ route('admin.source-categories.index') }}">
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
                        @if ($item['type'] === 'ellipsis')
                            <button class="pager-btn" disabled>…</button>
                        @else
                            <a class="pager-btn {{ $item['active'] ? 'is-active' : '' }}" href="{{ $item['url'] }}">{{ $item['page'] }}</a>
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

            <form method="POST" action="{{ route('admin.source-categories.bulk-destroy') }}" data-bulk-delete-form>
                @csrf
                @method('DELETE')
                <div data-bulk-delete-inputs hidden></div>
            </form>
        </section>
    </section>
@endsection
