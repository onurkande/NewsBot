@extends('admin.layouts.master')

@section('title', 'Kaynak Düzenle')
@section('active', 'source-accounts')
@section('crumbs', 'Haber Toplama | Kaynak Düzenle')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Kaynağı"
        title="{{ $account->username }}"
        subtitle="Kaynak hesap bilgilerini düzenleyin."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" :href="route('admin.source-accounts.index')">Listeye dön</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Twscrape Kaynağı" title="{{ $account->username }}">
        <form method="POST" action="{{ route('admin.source-accounts.update', $account) }}">
            @method('PUT')
            @include('admin.source-accounts._form')
        </form>
    </x-admin.card>
@endsection
