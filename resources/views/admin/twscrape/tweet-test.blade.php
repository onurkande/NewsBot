@extends('admin.layouts.master')

@section('title', 'Twscrape Tweet Testi')
@section('active', 'twscrape-tweet-test')
@section('crumbs', 'Twscrape Yonetimi | Tweet Testi')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape"
        title="Tweet Testi"
        subtitle="Bir tweet URL'si veya ID'si girerek twscrape ile tweet detaylarini test edin."
    />

    <x-admin.flash-message />

    @if (session('test_result'))
        @php $r = session('test_result'); @endphp
        <x-admin.card eyebrow="Sonuc" title="Test Sonucu" style="margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; line-height: 1.8;">
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">URL / ID</div>
                    <div style="font-weight: 600; word-break: break-all;">{{ $r['url'] }}</div>
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Durum</div>
                    @if ($r['success'])
                        <span class="tag t-active">Basarili</span>
                    @else
                        <span class="tag t-danger">Basarisiz</span>
                    @endif
                </div>
            </div>

            @if ($r['error'])
                <div style="margin-top: 16px; padding: 12px; background: rgba(244,63,94,0.08); border-radius: 6px; border-left: 3px solid var(--danger);">
                    <div style="color: var(--danger); font-weight: 600; font-size: 12px; margin-bottom: 4px;">Hata</div>
                    <pre style="font-size: 12px; color: var(--danger); white-space: pre-wrap; margin: 0;">{{ $r['error'] }}</pre>
                </div>
            @endif

            @if ($r['tweet'])
                <div style="margin-top: 20px;">
                    <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 8px;">Tweet Detaylari</div>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px;">
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Tweet ID</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['tweet_id'] ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Kullanici</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['user']['username'] ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Display Name</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['user']['display_name'] ?? '—' }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Like</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['like_count'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Retweet</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['retweet_count'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Reply</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['reply_count'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">View</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['view_count'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Quote</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['quote_count'] ?? 0 }}</div>
                        </div>
                        <div>
                            <div style="color: var(--t-muted); font-size: 11px;">Tarih</div>
                            <div style="font-weight: 600;">{{ $r['tweet']['tweeted_at'] ?? '—' }}</div>
                        </div>
                    </div>

                    <div style="margin-top: 16px;">
                        <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 4px;">Metin</div>
                        <div style="background: var(--bg-3); padding: 16px; border-radius: 6px; font-size: 14px; line-height: 1.6; white-space: pre-wrap;">
                            {{ $r['tweet']['text'] ?? '—' }}
                        </div>
                    </div>

                    @if (!empty($r['tweet']['photo_urls']))
                        <div style="margin-top: 12px;">
                            <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 4px;">Fotograf URL'leri</div>
                            @foreach ($r['tweet']['photo_urls'] as $photoUrl)
                                <div style="font-size: 12px; word-break: break-all; margin-bottom: 2px;">{{ $photoUrl }}</div>
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($r['tweet']['video_urls']))
                        <div style="margin-top: 12px;">
                            <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 4px;">Video URL'leri</div>
                            @foreach ($r['tweet']['video_urls'] as $videoUrl)
                                <div style="font-size: 12px; word-break: break-all; margin-bottom: 2px;">{{ $videoUrl }}</div>
                            @endforeach
                        </div>
                    @endif

                    <div style="margin-top: 16px;">
                        <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 4px;">Ham JSON</div>
                        <pre style="font-size: 11px; background: var(--bg-3); padding: 12px; border-radius: 6px; overflow-x: auto; max-height: 400px; overflow-y: auto;">{{ json_encode($r['tweet'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
                </div>
            @endif
        </x-admin.card>
    @endif

    <x-admin.card eyebrow="Form" title="Tweet Testi">
        <form method="POST" action="{{ route('admin.twscrape.tweet-test.fetch') }}" class="form-grid">
            @csrf
            <div class="form-row">
                <x-admin.form.input
                    name="url"
                    label="Tweet URL veya Tweet ID"
                    :value="old('url', '')"
                    placeholder="https://x.com/kullanici/status/1234567890"
                    required
                />
            </div>
            <div class="form-actions" style="margin-top: 24px;">
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Tweet Cek
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>
@endsection
