@extends('admin.layouts.master')

@section('title', 'İşlem Logları')
@section('active', 'twscrape-logs')
@section('crumbs', 'Twscrape Yönetimi | Loglar')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Yönetimi"
        title="İşlem Logları"
        subtitle="Twscrape işlemleri, komutları ve oluşan hatalar."
    />

    <x-admin.card eyebrow="Liste" title="Tüm Loglar">
        <form method="GET" action="{{ route('admin.twscrape.logs.index') }}" class="data-toolbar">
            <div class="data-toolbar-left">
                <div class="input-icon" style="flex: 1; max-width: 320px;">
                    <span class="ico">
                        <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input class="input" type="search" name="q" value="{{ $search }}" placeholder="Komut veya çıktı ara...">
                </div>
                <button class="btn btn--ghost" type="submit">Filtrele</button>
            </div>
            <div class="data-toolbar-right">
                <select class="select" name="type" onchange="this.form.submit()">
                    <option value="all" @selected($type === 'all')>Tümü</option>
                    <option value="command" @selected($type === 'command')>Command</option>
                    <option value="login" @selected($type === 'login')>Login</option>
                    <option value="fetch" @selected($type === 'fetch')>Fetch</option>
                </select>
            </div>
        </form>

        <div style="overflow-x: auto; margin: 0 -22px;">
            <table class="data-table" style="margin: 0 22px; min-width: 1000px;">
                <thead>
                    <tr>
                        <th style="width:160px">Tarih</th>
                        <th style="width:100px">Tip</th>
                        <th style="width:100px">Durum</th>
                        <th>Komut / Çıktı</th>
                        <th style="width:100px">Süre (ms)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr class="data-row">
                            <td class="data-cell-mono">{{ $log->created_at->format('d.m.Y H:i:s') }}</td>
                            <td><span class="badge secondary">{{ $log->type }}</span></td>
                            <td>
                                @if($log->is_successful)
                                    <span class="badge success">Başarılı</span>
                                @else
                                    <span class="badge danger">Hata</span>
                                @endif
                            </td>
                            <td>
                                @if($log->command)
                                    <div class="data-cell-mono" style="font-weight:bold; color:var(--t-base);">{{ $log->command }}</div>
                                @endif
                                <div style="max-height: 100px; overflow-y: auto; background:#f9f9f9; padding:4px 8px; border-radius:4px; font-size:12px; margin-top:4px;">
                                    <pre style="margin:0; white-space: pre-wrap;">{{ Str::limit($log->output ?: $log->error, 300) }}</pre>
                                </div>
                            </td>
                            <td class="data-cell-mono">{{ $log->duration_ms }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-admin.empty-state title="Log bulunamadı" description="Henüz kaydedilmiş işlem yok." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="data-foot">
            <div class="data-foot-info">
                {{ $logs->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </x-admin.card>
@endsection
