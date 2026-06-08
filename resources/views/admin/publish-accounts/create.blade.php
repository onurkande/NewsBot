@extends('admin.layouts.master')

@section('title', 'Yeni Yayin Hesabi')
@section('active', 'publish-accounts')
@section('crumbs', 'Yayin Yonetimi | Hesaplar | Yeni')

@section('content')
    <x-admin.page-header
        eyebrow="Yeni"
        title="Yayin Hesabi Olustur"
        subtitle="Tweet paylasilacak X hesabini ekleyin."
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
        <form method="POST" action="{{ route('admin.publish-accounts.store') }}" class="form-grid">
            @csrf
            @include('admin.publish-accounts._form')
            <div class="form-actions" style="margin-top: 24px;">
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                    Kaydet
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>
@endsection
