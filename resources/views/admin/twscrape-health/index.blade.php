@extends('admin.layouts.master')

@section('title', 'Sağlık Durumu')
@section('active', 'twscrape-health')
@section('crumbs', 'Twscrape Yönetimi | Sağlık Durumu')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Yönetimi"
        title="Sağlık Durumu"
        subtitle="Twscrape servisinin ve veritabanının anlık sağlık durumunu izleyin."
    />

    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:24px; margin-bottom:24px;">
        <x-admin.card eyebrow="Durum" title="accounts.db Erişimi" style="border-top: 4px solid {{ $stats['db_readable'] ? 'var(--c-success)' : 'var(--c-danger)' }}">
            <h2 style="font-size:24px; margin:0;">{{ $stats['db_readable'] ? 'ERİŞİLEBİLİR' : 'ERİŞİM YOK' }}</h2>
            <p style="color:var(--t-muted); font-size:12px; margin-top:8px;">{{ $stats['db_path'] }}</p>
        </x-admin.card>
        
        <x-admin.card eyebrow="Hesaplar" title="Aktif Hesaplar">
            <h2 style="font-size:32px; margin:0; color:var(--c-success);">{{ $stats['active_accounts'] }}</h2>
            <p style="color:var(--t-muted); font-size:12px; margin-top:8px;">Toplam {{ $stats['total_accounts'] }} hesaptan</p>
        </x-admin.card>

        <x-admin.card eyebrow="Hesaplar" title="Hatalı Hesaplar">
            <h2 style="font-size:32px; margin:0; color:var(--c-danger);">{{ $stats['error_accounts'] }}</h2>
            <p style="color:var(--t-muted); font-size:12px; margin-top:8px;">1 veya daha fazla hata almış</p>
        </x-admin.card>

        <x-admin.card eyebrow="Zaman" title="Son Scraping">
            <h2 style="font-size:20px; margin:0;">{{ $stats['last_scrape_at'] ? \Carbon\Carbon::parse($stats['last_scrape_at'])->diffForHumans() : 'Yok' }}</h2>
            <p style="color:var(--t-muted); font-size:12px; margin-top:8px;">{{ $stats['last_scrape_at'] }}</p>
        </x-admin.card>
    </div>

@endsection
