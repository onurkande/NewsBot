@extends('admin.layouts.master')

@section('title', 'Kaynak Hesaplar')
@section('active', 'source-accounts')
@section('crumbs', 'Haber Toplama | Kaynak Hesaplar')

@section('content')
    <section class="hero">
        <div class="hero-text">
            <span class="eyebrow">Twscrape</span>
            <h1 class="hero-title">Kaynak hesaplar</h1>
            <p class="hero-sub">Taranacak X hesaplarini, guven puanlarini ve kontrol araliklarini buradan yonetin.</p>
        </div>
        <div class="hero-actions">
            <a class="btn btn--primary" href="{{ route('admin.source-accounts.create') }}">
                <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14" /></svg>
                Yeni kaynak
            </a>
        </div>
    </section>

    @include('admin.partials.flash')

    <section class="card">
        <form class="data-toolbar" method="GET" action="{{ route('admin.source-accounts.index') }}">
            <div class="data-toolbar-left">
                <div class="input-icon">
                    <span class="ico"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" /></svg></span>
                    <input class="input" name="q" value="{{ request('q') }}" placeholder="Kullanici adi ara">
                </div>
                <select class="select" name="status">
                    <option value="">Tum durumlar</option>
                    <option value="active" @selected(request('status') === 'active')>Aktif</option>
                    <option value="passive" @selected(request('status') === 'passive')>Pasif</option>
                </select>
            </div>
            <div class="data-toolbar-right">
                <button class="btn btn--secondary" type="submit">Filtrele</button>
            </div>
        </form>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Hesap</th>
                        <th>Kategori</th>
                        <th>Guven</th>
                        <th>Oncelik</th>
                        <th>Aralik</th>
                        <th>Son kontrol</th>
                        <th>Durum</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr>
                            <td>
                                <div class="data-cell-user">
                                    <div class="av" style="background: var(--primary)">{{ Str::upper(Str::substr($account->username, 0, 2)) }}</div>
                                    <div class="data-cell-user-meta">
                                        <div class="data-cell-user-name">{{ $account->display_name ?: '@'.$account->username }}</div>
                                        <div class="data-cell-user-email">@{{ $account->username }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $account->category?->name ?: '-' }}</td>
                            <td><span class="badge success">{{ $account->trust_score }}/100</span></td>
                            <td><span class="badge primary">{{ $account->priority_score }}/100</span></td>
                            <td class="data-cell-mono">{{ $account->check_interval_minutes }} dk</td>
                            <td class="data-cell-mono">{{ $account->last_checked_at?->diffForHumans() ?: 'Bekliyor' }}</td>
                            <td>
                                <span class="tag {{ $account->is_active ? 't-active' : 't-unavail' }}">{{ $account->is_active ? 'Aktif' : 'Pasif' }}</span>
                            </td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <form method="POST" action="{{ route('admin.source-accounts.fetch', $account) }}">
                                        @csrf
                                        <button class="btn btn--icon" type="submit" aria-label="Topla">
                                            <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><path d="M7 10l5 5 5-5" /><path d="M12 15V3" /></svg>
                                        </button>
                                    </form>
                                    <a class="btn btn--icon" href="{{ route('admin.source-accounts.edit', $account) }}" aria-label="Duzenle">
                                        <svg viewBox="0 0 24 24"><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 1 1 3 3L7 19l-4 1 1-4z" /></svg>
                                    </a>
                                    <form method="POST" action="{{ route('admin.source-accounts.destroy', $account) }}" onsubmit="return confirm('Bu kaynak hesap silinsin mi?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn--icon" type="submit" aria-label="Sil">
                                            <svg viewBox="0 0 24 24"><path d="M3 6h18" /><path d="M8 6V4h8v2" /><path d="M19 6l-1 14H6L5 6" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">Henüz kaynak hesap yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-foot">
            <span>{{ $accounts->total() }} kayit</span>
            {{ $accounts->links() }}
        </div>
    </section>
@endsection
