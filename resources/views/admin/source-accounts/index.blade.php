@extends('admin.layouts.master')

@section('title', 'Kaynak Hesaplar')
@section('active', $selectionKey)
@section('crumbs', 'Haber Toplama | Kaynak Hesaplar')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape"
        title="Kaynak hesaplar"
        subtitle="Taranacak X hesaplarını, güven puanlarını ve kontrol aralıklarını buradan yönetin."
    >
        <x-slot:actions>
            <x-admin.button variant="primary" :href="route('admin.source-accounts.create')">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" /></svg>
                Yeni kaynak
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Hesaplar">
        <x-slot:actions>
            <span class="badge primary" data-selected-count data-selection-key="{{ $selectionKey }}">0 seçili</span>

            <button
                type="button"
                class="btn btn--danger"
                data-bulk-delete-trigger
                data-selection-key="{{ $selectionKey }}"
                data-confirm-delete-url="{{ route('admin.source-accounts.bulk-destroy') }}"
                data-confirm-delete-title="Seçili hesaplar silinsin mi?"
                data-confirm-delete-message="Seçtiğiniz kayıtlar geri alınamaz şekilde silinecek."
                disabled
            >
                <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M19 6l-1 14H6L5 6" /></svg>
                Toplu sil
            </button>
        </x-slot:actions>

        <form method="GET" action="{{ route('admin.source-accounts.index') }}" class="data-toolbar">
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
                        placeholder="Hesap adı veya görünen ad ara..."
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
                style="margin: 0 22px; min-width: 1100px;"
            >
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

                        <th>Durum</th>
                        <th style="text-align: right">İşlemler</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($accounts as $account)
                        <tr class="data-row" data-row-id="{{ $account->id }}">
                            <td>
                                <label class="check">
                                    <input type="checkbox" data-row-checkbox data-row-id="{{ $account->id }}">
                                    <span class="box"></span>
                                </label>
                            </td>

                            <td>
                                <div class="data-cell-user">
                                    <div class="av" style="background: var(--primary)">{{ Str::upper(Str::substr($account->username, 0, 2)) }}</div>
                                    <div class="data-cell-user-meta">
                                        <div class="data-cell-user-name">{{ $account->display_name ?: '@' . $account->username }}</div>
                                        <div class="data-cell-user-email">{{ $account->username }}</div>
                                    </div>
                                </div>
                            </td>

                            <td>
                                <span class="badge success">{{ $account->trust_score }}/100</span>
                            </td>

                            <td>
                                <span class="badge primary">{{ $account->priority_score }}/100</span>
                            </td>

                            <td class="data-cell-mono">
                                {{ $account->min_check_interval_minutes ?: $account->check_interval_minutes }}-{{ $account->max_check_interval_minutes ?: $account->check_interval_minutes }} dk
                            </td>

                            <td>
                                @if ($account->last_checked_at)
                                    <span class="data-cell-mono">{{ $account->last_checked_at->diffForHumans() }}</span>
                                @else
                                    <span class="badge primary">Bekliyor</span>
                                @endif
                            </td>

                            <td>
                                @if ($account->next_check_at)
                                    <div class="data-cell-mono">{{ $account->next_check_interval_minutes ?: '-' }} dk</div>
                                    <div class="data-cell-user-email">{{ $account->next_check_at->format('Y-m-d H:i') }}</div>
                                @else
                                    <span class="badge primary">Sırada</span>
                                @endif
                            </td>

                            <td>
                                <span class="tag {{ $account->is_active ? 't-active' : 't-unavail' }}">
                                    {{ $account->is_active ? 'Aktif' : 'Pasif' }}
                                </span>
                            </td>

                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <form method="POST" action="{{ route('admin.source-accounts.fetch', $account) }}" style="display: inline;">
                                        @csrf
                                        <button class="btn--icon" type="submit" aria-label="Topla">
                                            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><path d="M7 10l5 5 5-5" /><path d="M12 15V3" /></svg>
                                        </button>
                                    </form>

                                    <a class="btn--icon" href="{{ route('admin.source-accounts.edit', $account) }}" aria-label="Düzenle">
                                        <svg viewBox="0 0 24 24"><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z" /></svg>
                                    </a>

                                    <button
                                        type="button"
                                        class="btn--icon"
                                        aria-label="Sil"
                                        data-confirm-delete-trigger
                                        data-confirm-delete-url="{{ route('admin.source-accounts.destroy', $account) }}"
                                        data-confirm-delete-title="Hesap silinsin mi?"
                                        data-confirm-delete-message="&quot;{{ $account->username }}&quot; kalıcı olarak silinecek."
                                        data-confirm-delete-label="{{ $account->username }}"
                                    >
                                        <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M19 6l-1 14H6L5 6" /></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <x-admin.empty-state
                                    title="Henüz kaynak hesap yok"
                                    description="Yeni bir kaynak hesabı oluşturarak başlayabilirsiniz."
                                >
                                    <x-slot:default>
                                        <div style="margin-top: 16px;">
                                            <x-admin.button :href="route('admin.source-accounts.create')" variant="primary">Yeni kaynak</x-admin.button>
                                        </div>
                                    </x-slot:default>
                                </x-admin.empty-state>
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

                <form method="GET" action="{{ route('admin.source-accounts.index') }}">
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

    <x-admin.modal
        id="bulkDeleteModal"
        title="Seçili hesapları sil"
        message="Onay verdiğinizde seçili kayıtlar geri alınamaz şekilde silinecek."
    >
        <form method="POST" action="{{ route('admin.source-accounts.bulk-destroy') }}" data-confirm-delete-form>
            @csrf
            @method('DELETE')

            <div data-confirm-delete-inputs></div>

            <div class="form-actions" style="margin-top: 0;">
                <x-admin.button variant="secondary" type="button" data-modal-close>Vazgeç</x-admin.button>
                <div class="spacer"></div>
                <x-admin.button variant="danger" type="submit">Sil</x-admin.button>
            </div>
        </form>
    </x-admin.modal>
@endsection
