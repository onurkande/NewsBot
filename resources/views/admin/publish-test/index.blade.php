@extends('admin.layouts.master')

@section('title', 'Yayin Testi')
@section('active', 'publish-test')
@section('crumbs', 'Yayin Yonetimi | Test')

@section('content')
    <x-admin.page-header
        eyebrow="Test"
        title="Gerçek Yayin Testi"
        subtitle="Secili hesap uzerinden gerçek tweet paylasimi yaparak PublishService baglantisini test edin."
    />

    <x-admin.flash-message />

    @if (session('test_result'))
        @php $r = session('test_result'); @endphp
        <x-admin.card eyebrow="Sonuc" title="Test Sonuclari" style="margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; line-height: 1.8;">
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Durum</div>
                    @if ($r['success'])
                        <span class="tag t-active">Basarili</span>
                    @else
                        <span class="tag t-danger">Basarisiz</span>
                    @endif
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Hesap</div>
                    <div style="font-weight: 600;">{{ $r['account'] }}</div>
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Sure</div>
                    <div style="font-weight: 600;">{{ $r['duration'] }} ms</div>
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Tweet ID</div>
                    <div style="font-weight: 600;">{{ $r['tweet_id'] ?? '—' }}</div>
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Medya Sayisi</div>
                    <div style="font-weight: 600;">{{ $r['media_count'] ?? 0 }}</div>
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Metin Uzunlugu</div>
                    <div style="font-weight: 600;">{{ mb_strlen($r['text'] ?? '') }} karakter</div>
                </div>
            </div>

            @if ($r['success'])
                <div style="margin-top: 16px; padding: 12px; background: rgba(16,185,129,0.08); border-radius: 6px; border-left: 3px solid var(--success);">
                    <div style="color: var(--success); font-weight: 600;">Tweet basariyla gonderildi!</div>
                    <div style="font-size: 13px; color: var(--t-muted); margin-top: 4px;">
                        Tweet ID: {{ $r['tweet_id'] }} | Hesap: @{{ $r['account'] }}
                    </div>
                </div>
            @endif

            @if ($r['error'])
                <div style="margin-top: 16px; padding: 12px; background: rgba(244,63,94,0.08); border-radius: 6px; border-left: 3px solid var(--danger);">
                    <div style="color: var(--danger); font-weight: 600; font-size: 12px; margin-bottom: 4px;">Hata Mesaji</div>
                    <div style="font-size: 13px; color: var(--danger); line-height: 1.6;">{{ $r['error'] }}</div>
                </div>
            @endif

            @if ($r['raw'])
                <div style="margin-top: 16px;">
                    <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 4px;">Python Ciktisi (Raw Response)</div>
                    <pre style="font-size: 11px; background: var(--bg-3); padding: 12px; border-radius: 6px; overflow-x: auto; max-height: 400px; overflow-y: auto;">{{ json_encode($r['raw'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif

            <div style="margin-top: 16px;">
                <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 4px;">Gonderilen Metin</div>
                <div style="background: var(--bg-3); padding: 16px; border-radius: 6px; font-size: 14px; line-height: 1.6; white-space: pre-wrap;">{{ $r['text'] }}</div>
            </div>
        </x-admin.card>
    @endif

    <x-admin.card eyebrow="Form" title="Test Paylasimi">
        <div class="body-text" style="margin-bottom: 16px; padding: 12px; background: rgba(245,158,11,0.08); border-radius: 6px; border-left: 3px solid var(--warning, #f59e0b); font-size: 13px;">
            <strong>Uyari:</strong> Bu form gerçek tweet gonderir. Hesap bilgilerinin (auth_token, ct0) dogru oldugundan emin olun.
        </div>

        <form method="POST" action="{{ route('admin.publish-test.store') }}" class="form-grid">
            @csrf
            <div class="form-row">
                <x-admin.form.select
                    name="publish_account_id"
                    label="Hesap"
                    :options="$accounts->mapWithKeys(fn($a) => [
                        $a->id => $a->username
                            . ($a->is_active ? '' : ' (Pasif)')
                            . (empty($a->auth_token) ? ' [Token Yok]' : '')
                    ])->toArray()"
                    :selected="old('publish_account_id')"
                    required
                />
            </div>

            <div class="form-row">
                <x-admin.form.textarea
                    name="text"
                    label="Tweet Metni (maksimum 280 karakter)"
                    :value="old('text', 'Bu bir test tweetidir. #NewsBot')"
                    rows="4"
                    required
                />
            </div>

            <div class="form-row">
                <x-admin.form.textarea
                    name="media_paths"
                    label="Medya Yollari (JSON array, opsiyonel)"
                    :value="old('media_paths', '')"
                    rows="2"
                />
                <div style="color: var(--t-muted); font-size: 12px; margin-top: 4px;">
                    Ornek: ["storage/app/media/tweets/123/a.jpg"]
                </div>
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                    Gerçek Tweet Gonder
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>
@endsection
