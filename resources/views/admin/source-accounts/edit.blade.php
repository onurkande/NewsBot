@extends('admin.layouts.master')

@section('title', 'Kaynak Duzenle')
@section('active', 'source-accounts')
@section('crumbs', 'Haber Toplama | Kaynak Duzenle')

@section('content')
    <section class="card">
        <div class="card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Twscrape Kaynagi</span>
                <h2 class="card-title">@{{ $account->username }}</h2>
            </div>
        </div>

        @include('admin.partials.flash')

        <form method="POST" action="{{ route('admin.source-accounts.update', $account) }}">
            @method('PUT')
            @include('admin.source-accounts._form')
        </form>
    </section>
@endsection
