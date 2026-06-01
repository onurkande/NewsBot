@extends('admin.layouts.master')

@section('title', 'Kullanım İstatistikleri')
@section('active', 'twscrape-stats')
@section('crumbs', 'Twscrape Yönetimi | İstatistikler')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Yönetimi"
        title="Kullanım İstatistikleri"
        subtitle="Hangi hesabın ne kadar kullanıldığını inceleyin."
    />

    <x-admin.card eyebrow="Analiz" title="Kullanım Dağılımı">
        <div style="overflow-x: auto; margin: 0 -22px;">
            <table class="data-table" style="margin: 0 22px; min-width: 600px;">
                <thead>
                    <tr>
                        <th>Kullanıcı Adı</th>
                        <th>Kullanım (Adet)</th>
                        <th>Hata (Adet)</th>
                        <th>Ağırlık</th>
                        <th>Oran</th>
                    </tr>
                </thead>
                <tbody>
                    @php $total = max(1, $accounts->sum('total_usage')); @endphp
                    @forelse ($accounts as $account)
                        @php $percent = round(($account->total_usage / $total) * 100, 1); @endphp
                        <tr class="data-row">
                            <td><strong>{{ $account->username }}</strong></td>
                            <td>{{ $account->total_usage }}</td>
                            <td><span style="color:var(--c-danger)">{{ $account->error_count }}</span></td>
                            <td>{{ $account->weight }}</td>
                            <td style="width: 40%;">
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <div style="flex:1; background:#eee; height:8px; border-radius:4px; overflow:hidden;">
                                        <div style="background:var(--c-primary); height:100%; width:{{ $percent }}%;"></div>
                                    </div>
                                    <span style="font-size:12px; font-variant-numeric: tabular-nums;">%{{ $percent }}</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><x-admin.empty-state title="Veri yok" description="Henüz kullanım verisi oluşmamış." /></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
@endsection
