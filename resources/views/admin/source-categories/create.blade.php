@extends('admin.layouts.master')

@section('title', 'Yeni Kategori')
@section('active', 'source-categories')
@section('crumbs', 'Haber Toplama | Yeni Kategori')

@section('content')
    <x-admin.page-header
        eyebrow="Haber Toplama"
        title="Yeni kategori"
        subtitle="Kaynak kategorilerini tek tek, düzenli bir yapı ile oluşturun."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" :href="route('admin.source-categories.index')">Listeye dön</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Kaynak kategorisi" title="Yeni kategori">
        <form method="POST" action="{{ route('admin.source-categories.store') }}">
            @include('admin.source-categories._form')
        </form>
    </x-admin.card>
@endsection
