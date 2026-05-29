@extends('admin.layouts.master')

@section('title', 'Kategori Düzenle')
@section('active', 'source-categories')
@section('crumbs', 'Haber Toplama | Kategori Düzenle')

@section('content')
    <x-admin.page-header
        eyebrow="Haber Toplama"
        title="{{ $category->name }}"
        subtitle="Kategori bilgilerini güncelleyin."
    >
        <x-slot:actions>
            <x-admin.button variant="secondary" :href="route('admin.source-categories.index')">Listeye dön</x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Kaynak kategorisi" title="Düzenle">
        <form method="POST" action="{{ route('admin.source-categories.update', $category) }}">
            @method('PUT')
            @include('admin.source-categories._form')
        </form>
    </x-admin.card>
@endsection
