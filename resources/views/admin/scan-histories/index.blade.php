@extends('admin.layouts.master')

@section('title', 'Tarama Geçmişi')
@section('active', $selectionKey)
@section('crumbs', 'Haber Toplama | Tarama Geçmişi')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape İzleme"
        title="Tarama geçmişi"
        subtitle="Sistemdeki tüm kaynak taramalarının sonuçlarını, sürelerini ve hata durumlarını tek ekrandan takip edin."
    >
    </x-admin.page-header>

    <x-admin.flash-message />

    @include('admin.scan-histories._table', [
        'historyAction' => route('admin.scan-histories.index'),
        'tableTitle' => 'Tüm taramalar',
        'showClusterColumns' => true,
    ])
@endsection
