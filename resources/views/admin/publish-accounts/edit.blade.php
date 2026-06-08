@extends('admin.layouts.master')

@section('title', 'Yayin Hesabi Duzenle')
@section('active', 'publish-accounts')
@section('crumbs', 'Yayin Yonetimi | Hesaplar | Duzenle')

@section('content')
    <x-admin.page-header
        eyebrow="Duzenle"
        title="Hesap Duzenle: {{ $account->username }}"
        subtitle="Hesap bilgilerini guncelleyin."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.publish-accounts.index')">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Listeye Don
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Form" title="Hesap Bilgileri">
        <form method="POST" action="{{ route('admin.publish-accounts.update', $account) }}" class="form-grid">
            @csrf
            @method('PUT')
            @include('admin.publish-accounts._form', ['account' => $account])
            <div class="form-actions" style="margin-top: 24px;">
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Guncelle
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>
@endsection
