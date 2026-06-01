@extends('admin.layouts.master')

@section('title', 'Hesap Detayı - ' . $account->username)
@section('active', 'twscrape-accounts')
@section('crumbs', 'Twscrape Yönetimi | Hesaplar | Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Hesap Detayı"
        title="{{ $account->username }}"
        subtitle="Bu hesabın SQLite ve yerel kayıtlarındaki tüm bilgileri."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" :href="route('admin.twscrape.accounts.index')">Geri Dön</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
        <x-admin.card eyebrow="Bilgiler" title="Laravel Kayıtları (twscrape_accounts)">
            <ul style="line-height: 2; font-size: 14px;">
                <li><strong>Durum:</strong> {{ $account->is_active ? 'Aktif' : 'Pasif' }} ({{ $account->status }})</li>
                <li><strong>Weight:</strong> {{ $account->weight }}</li>
                <li><strong>Toplam Kullanım:</strong> {{ $account->total_usage }}</li>
                <li><strong>Hata Sayısı:</strong> <span style="color: {{ $account->error_count > 0 ? 'var(--c-danger)' : 'inherit' }}">{{ $account->error_count }}</span></li>
                <li><strong>Son Kullanım:</strong> {{ $account->last_used_at ?? 'Hiç kullanılmadı' }}</li>
                <li><strong>Son Başarılı:</strong> {{ $account->last_success_at ?? '-' }}</li>
                <li><strong>Son Hata:</strong> {{ $account->last_error_at ?? '-' }}</li>
                <li><strong>Son Hata Mesajı:</strong> <pre style="background:#f4f4f4; padding:8px; border-radius:4px; max-height: 100px; overflow:auto;">{{ $account->last_error_message ?? '-' }}</pre></li>
            </ul>
        </x-admin.card>

        <x-admin.card eyebrow="Bilgiler" title="SQLite Kayıtları (accounts.db)">
            <ul style="line-height: 2; font-size: 14px;">
                <li><strong>Email:</strong> {{ $sqliteAccount->email ?? 'Bilinmiyor' }}</li>
                <li><strong>Aktif (SQLite):</strong> {{ ($sqliteAccount->active ?? false) ? 'Evet' : 'Hayır' }}</li>
                <li><strong>Error Msg:</strong> <pre style="background:#f4f4f4; padding:8px; border-radius:4px; max-height: 100px; overflow:auto;">{{ $sqliteAccount->error_msg ?? '-' }}</pre></li>
                <li><strong>Stats:</strong> <pre style="background:#f4f4f4; padding:8px; border-radius:4px; max-height: 150px; overflow:auto;">{{ $sqliteAccount->stats ?? '{}' }}</pre></li>
                <li><strong>MFA Code:</strong> {{ $sqliteAccount->mfa_code ?? '-' }}</li>
            </ul>
        </x-admin.card>
    </div>
@endsection
