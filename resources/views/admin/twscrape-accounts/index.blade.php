@extends('admin.layouts.master')

@section('title', 'Twscrape Hesapları')
@section('active', 'twscrape-accounts')
@section('crumbs', 'Twscrape Yönetimi | Hesaplar')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Yönetimi"
        title="Hesaplar"
        subtitle="Sisteme kayıtlı Twscrape hesaplarını ve durumlarını yönetin."
    >
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Liste" title="Twscrape Hesapları">
        <form method="GET" action="{{ route('admin.twscrape.accounts.index') }}" class="data-toolbar">
            <div class="data-toolbar-left">
                <div class="input-icon" style="flex: 1; max-width: 320px;">
                    <span class="ico">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Kullanıcı adı ara...">
                </div>
                <button class="btn btn--ghost" type="submit">Filtrele</button>
            </div>
            <div class="data-toolbar-right">
                <select class="select" name="status" onchange="this.form.submit()">
                    <option value="all" @selected($status === 'all')>Tümü</option>
                    <option value="active" @selected($status === 'active')>Aktif</option>
                    <option value="passive" @selected($status === 'passive')>Pasif</option>
                </select>
            </div>
        </form>

        <div style="overflow-x: auto; margin: 0 -22px;">
            <table class="data-table" style="margin: 0 22px; min-width: 1000px;">
                <thead>
                    <tr>
                        <th>Kullanıcı Adı</th>
                        <th>Durum</th>
                        <th>Weight</th>
                        <th>Toplam Kullanım</th>
                        <th>Hatalar</th>
                        <th>Son Kullanım</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($accounts as $account)
                        <tr class="data-row">
                            <td>
                                <strong>{{ $account->username }}</strong><br>
                                <small class="data-cell-mono" style="color:var(--t-muted)">Oluşturulma: {{ $account->created_at->format('d.m.Y H:i') }}</small>
                            </td>
                            <td>
                                @if($account->is_active)
                                    <span class="badge success">Aktif</span>
                                @else
                                    <span class="badge danger">Pasif</span>
                                @endif
                                <br><small>{{ $account->status }}</small>
                            </td>
                            <td>
                                <form action="{{ route('admin.twscrape.accounts.weight', $account->username) }}" method="POST" style="display:flex; gap:4px; align-items:center;">
                                    @csrf @method('PUT')
                                    <input type="number" name="weight" value="{{ $account->weight }}" class="input" style="width:60px; padding: 2px 4px; height:28px;">
                                    <button type="submit" class="btn btn--ghost btn--icon" style="height:28px; width:28px;"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg></button>
                                </form>
                            </td>
                            <td>{{ $account->total_usage }}</td>
                            <td>
                                <span style="color: {{ $account->error_count > 0 ? 'var(--c-danger)' : 'inherit' }}">{{ $account->error_count }}</span>
                            </td>
                            <td>
                                <small>Son İşlem: {{ $account->last_used_at ? $account->last_used_at->diffForHumans() : '-' }}</small><br>
                                <small>Başarılı: {{ $account->last_success_at ? $account->last_success_at->format('d.m.Y H:i') : '-' }}</small>
                            </td>
                            <td>
                                <div class="data-cell-actions">
                                    <a class="btn--icon" href="{{ route('admin.twscrape.accounts.show', $account->username) }}" title="Detay">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <form action="{{ route('admin.twscrape.accounts.toggle', $account->username) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <button class="btn--icon" title="Aktif/Pasif Yap" type="submit">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"></path><line x1="12" y1="2" x2="12" y2="12"></line></svg>
                                        </button>
                                    </form>
                                    <a class="btn--icon" title="Yeniden Login" href="{{ route('admin.twscrape.commands.index') }}?cmd=relogin&acc={{ $account->username }}">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7"><x-admin.empty-state title="Hesap bulunamadı" description="Kayıtlı twscrape hesabı yok." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="data-foot">
            <div class="data-foot-info">
                {{ $accounts->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </x-admin.card>
@endsection
