@extends('admin.layouts.master')

@section('title', 'Yeni Kategori')
@section('active', 'source-categories')
@section('crumbs', 'Haber Toplama | Yeni Kategori')

@section('content')
    <section class="card">
        <div class="card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Kaynak Kategorisi</span>
                <h2 class="card-title">Yeni kategori</h2>
            </div>
        </div>

        @include('admin.partials.flash')

        <form method="POST" action="{{ route('admin.source-categories.store') }}">
            @include('admin.source-categories._form')
        </form>
    </section>
@endsection
