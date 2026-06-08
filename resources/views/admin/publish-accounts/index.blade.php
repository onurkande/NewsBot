@extends('admin.layouts.master')

@section('title', 'Yayin Hesaplari')
@section('active', 'publish-accounts')
@section('crumbs', 'Yayin Yonetimi | Hesaplar')

@section('content')
    <x-admin.page-header
        eyebrow="Hesaplar"
        title="Yayin Hesaplari"
        subtitle="Tweet paylasilacak X hesaplarini yonetin."
    >
        <x-slot:actions>
            <x-admin.button variant="primary" :href="route('admin.publish-accounts.create')">
                <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Yeni Hesap
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Hesaplar">
        <form method="GET" action="{{ route('admin.publish-accounts.index') }}" class="data-toolbar">
            <div class="data-toolbar-left">
                <div class="input-icon" style="flex: 1; max-width: 320px;">
                    <span class="ico">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Kullanici adi ara...">
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
                        <th>Kullanici Adi</th>
                        <th>Display Name</th>
                        <th>Takipci</th>
                        <th>Takip</th>
                        <th>Tweet</th>
                        <th>Durum</th>
                        <th>Son Senk.</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr class="data-row">
                            <td class="data-cell-mono">{{ $account->id }}</td>
                            <td>
                                <span style="font-weight: 600;">{{ $account->username }}</span>
                            </td>
                            <td>{{ $account->display_name ?? '—' }}</td>
                            <td>{{ number_format($account->followers_count) }}</td>
                            <td>{{ number_format($account->following_count) }}</td>
                            <td>{{ number_format($account->statuses_count) }}</td>
                            <td>
                                @if ($account->is_active)
                                    <span class="tag t-active">Aktif</span>
                                @else
                                    <span class="tag t-unavail">Pasif</span>
                                @endif
                            </td>
                            <td>
                                {{ $account->last_synced_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <form method="POST" action="{{ route('admin.publish-accounts.sync', $account) }}" style="display:inline">
                                        @csrf
                                        <button class="btn--icon" type="submit" aria-label="Senkronize Et" title="Bilgileri Guncelle">
                                            <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                                        </button>
                                    </form>
                                    <a class="btn--icon" href="{{ route('admin.publish-accounts.edit', $account) }}" aria-label="Duzenle">
                                        <svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.publish-accounts.destroy', $account) }}" style="display:inline" onsubmit="return confirm('Silmek istediginize emin misiniz?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn--icon" type="submit" aria-label="Sil" style="color: var(--danger);">
                                            <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-admin.empty-state title="Henuz hesap yok" description="Yeni bir yayin hesabi ekleyin." />
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
                <form method="GET" action="{{ route('admin.publish-accounts.index') }}">
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
