@extends('admin.layouts.master')

@section('title', 'Yeni Kaynak')
@section('active', 'source-accounts')
@section('crumbs', 'Haber Toplama | Yeni Kaynak')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Kaynağı"
        title="Yeni kaynak hesap"
        subtitle="X haber kaynaklarına yeni hesap ekleyin."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" :href="route('admin.source-accounts.index')">Listeye dön</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Twscrape Kaynağı" title="Yeni kaynak hesap">
        <form method="POST" action="{{ route('admin.source-accounts.store') }}">
            @include('admin.source-accounts._form')
        </form>
    </x-admin.card>
@endsection
