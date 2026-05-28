@extends('admin.layouts.master')

@section('title', 'Ham Tweetler')
@section('active', 'raw-tweets')
@section('crumbs', 'Haber Toplama | Ham Tweetler')

@section('content')
    <section class="card">
        <div class="card-head">
            <div class="card-title-wrap">
                <span class="eyebrow">Twscrape Ciktisi</span>
                <h2 class="card-title">Ham tweetler</h2>
            </div>
        </div>

        <form class="data-toolbar" method="GET" action="{{ route('admin.raw-tweets.index') }}">
            <div class="data-toolbar-left">
                <div class="input-icon">
                    <span class="ico"><svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" /></svg></span>
                    <input class="input" name="q" value="{{ request('q') }}" placeholder="Tweet metni veya ID ara">
                </div>
            </div>
            <div class="data-toolbar-right">
                <button class="btn btn--secondary" type="submit">Ara</button>
            </div>
        </form>

        <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tweet</th>
                        <th>Kaynak</th>
                        <th>Story</th>
                        <th>Etkilesim</th>
                        <th>Tarih</th>
                        <th>Durum</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tweets as $tweet)
                        <tr>
                            <td style="white-space: normal; min-width: 360px">
                                <div class="data-cell-user-name">{{ Str::limit($tweet->tweet_text, 140) }}</div>
                                <div class="data-cell-mono">{{ $tweet->tweet_id }}</div>
                            </td>
                            <td>@{{ $tweet->sourceAccount->username }}</td>
                            <td>{{ $tweet->storyClusterItem?->storyCluster?->title ? Str::limit($tweet->storyClusterItem->storyCluster->title, 60) : '-' }}</td>
                            <td class="data-cell-mono">L {{ $tweet->like_count }} / RT {{ $tweet->retweet_count }} / V {{ $tweet->view_count }}</td>
                            <td class="data-cell-mono">{{ $tweet->tweeted_at?->format('Y-m-d H:i') ?: '-' }}</td>
                            <td><span class="tag {{ $tweet->is_processed ? 't-active' : 't-old' }}">{{ $tweet->is_processed ? 'Islendi' : 'Bekliyor' }}</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">Henüz tweet yok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="data-foot">
            <span>{{ $tweets->total() }} kayit</span>
            {{ $tweets->links() }}
        </div>
    </section>
@endsection
