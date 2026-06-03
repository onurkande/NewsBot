@extends('admin.layouts.master')

@section('title', 'Prompt Duzenle')
@section('active', 'prompts')
@section('crumbs', 'AI Yonetimi | Prompt Yonetimi | Duzenle')

@section('content')
    <x-admin.page-header
        eyebrow="Duzenle"
        title="Prompt Duzenle: {{ $prompt->name }}"
        subtitle="Mevcut prompt sablonunu guncelleyin."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.prompts.index')">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Listeye Don
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Bilgiler" title="Prompt Detaylari">
        <form method="POST" action="{{ route('admin.prompts.update', $prompt) }}">
            @csrf
            @method('PUT')
            @include('admin.prompts._form')
        </form>
    </x-admin.card>
@endsection
