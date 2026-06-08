@extends('admin.layouts.master')

@section('title', 'AI Uretim Detayi')
@section('active', 'ai-generations')
@section('crumbs', 'AI Yonetimi | AI Uretimleri | Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Uretim Detay"
        title="Uretim #{{ $generation->id }}"
        subtitle="{{ $generation->generated_at?->format('Y-m-d H:i') }} | {{ $generation->provider }} | {{ $generation->model }} | v{{ $generation->prompt_version }}"
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.ai-generations.index')">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Uretimlere Don
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    @if ($generation->rawTweet)
        <x-admin.card eyebrow="Tweet" title="Kaynak Tweet">
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid var(--primary);">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 8px;">
                    <div>
                        <span style="font-weight: 600; font-size: 14px;">{{ $generation->rawTweet->sourceAccount?->username ?? 'bilinmiyor' }}</span>
                        @if ($generation->rawTweet->sourceAccount?->display_name)
                            <span style="color: var(--t-muted); font-size: 13px; margin-left: 4px;">({{ $generation->rawTweet->sourceAccount->display_name }})</span>
                        @endif
                    </div>
                    <span class="data-cell-mono" style="font-size: 11px; color: var(--t-muted);">{{ $generation->rawTweet->tweet_id }}</span>
                </div>
                <div style="font-size: 14px; line-height: 1.6; margin-bottom: 12px;">{{ $generation->rawTweet->tweet_text }}</div>
                <div style="display: flex; gap: 16px; font-size: 12px; color: var(--t-muted);">
                    <span>{{ $generation->rawTweet->tweeted_at?->format('Y-m-d H:i') ?? '—' }}</span>
                    <span>Like: {{ $generation->rawTweet->like_count }}</span>
                    <span>RT: {{ $generation->rawTweet->retweet_count }}</span>
                    <span>Reply: {{ $generation->rawTweet->reply_count }}</span>
                    <span>View: {{ $generation->rawTweet->view_count }}</span>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 16px;">
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Kategori</div>
                    @if ($generation->category)
                        <span class="badge primary">{{ $generation->category->name }}</span>
                    @elseif ($generation->rawTweet?->sourceAccount?->category)
                        <span class="badge primary">{{ $generation->rawTweet->sourceAccount->category->name }}</span>
                    @else
                        <span style="color: var(--t-muted);">—</span>
                    @endif
                </div>
                <div>
                    <div style="color: var(--t-muted); font-size: 12px;">Kaynak Hesap</div>
                    <div style="font-weight: 600;">{{ $generation->rawTweet->sourceAccount?->username ?? '—' }}</div>
                </div>
            </div>
        </x-admin.card>
    @endif

    <x-admin.card eyebrow="Meta" title="Uretim Bilgileri" style="margin-top: 24px;">
        <div class="body-text" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; line-height: 1.8;">
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Provider</div>
                <div style="font-weight: 600;">{{ $generation->provider }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Model</div>
                <div style="font-weight: 600;">{{ $generation->model }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Prompt Versiyon</div>
                <div style="font-weight: 600;">v{{ $generation->prompt_version }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Sure</div>
                <div style="font-weight: 600;">{{ $generation->duration ? $generation->duration . ' ms' : '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Durum</div>
                @php
                    $statusTag = match($generation->status) {
                        'draft' => 't-unavail',
                        'approved' => 't-info',
                        'rejected' => 't-danger',
                        'publishing' => 't-warning',
                        'published' => 't-active',
                        'publish_failed' => 't-danger',
                        'expired' => 't-unavail',
                        default => 't-unavail',
                    };
                    $statusLabel = match($generation->status) {
                        'draft' => 'Taslak',
                        'approved' => 'Onaylandi',
                        'rejected' => 'Reddedildi',
                        'publishing' => 'Yayinlaniyor',
                        'published' => 'Yayinlandi',
                        'publish_failed' => 'Yayin Basarisiz',
                        'expired' => 'Suresi Doldu',
                        default => $generation->status,
                    };
                @endphp
                <span class="tag {{ $statusTag }}">{{ $statusLabel }}</span>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Onay Tarihi</div>
                <div style="font-weight: 600;">{{ $generation->approved_at?->format('Y-m-d H:i:s') ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Uretim Tarihi</div>
                <div style="font-weight: 600;">{{ $generation->generated_at?->format('Y-m-d H:i:s') ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Batch</div>
                <a href="{{ route('admin.ai-queue.show', $generation->aiQueue) }}" class="data-cell-mono" style="color: var(--link); font-weight: 600;">
                    {{ $generation->aiQueue->batch_no }}
                </a>
            </div>
        </div>

        @if ($generation->token_usage)
        <div style="margin-top: 16px; padding: 12px; background: var(--bg-3); border-radius: 6px;">
            <div style="color: var(--t-muted); font-size: 12px; margin-bottom: 8px;">Token Kullanimi</div>
            <div style="display: flex; gap: 24px;">
                <div><span style="font-weight: 600;">Prompt:</span> {{ $generation->token_usage['prompt_tokens'] ?? '—' }}</div>
                <div><span style="font-weight: 600;">Completion:</span> {{ $generation->token_usage['completion_tokens'] ?? '—' }}</div>
                <div><span style="font-weight: 600;">Total:</span> {{ $generation->token_usage['total_tokens'] ?? '—' }}</div>
            </div>
        </div>
        @endif
    </x-admin.card>

    @if ($generation->status === 'draft' || $generation->status === 'approved' || $generation->status === 'publish_failed')
        <x-admin.card eyebrow="Review" title="Durum Yonetimi" style="margin-top: 24px;">
            <div style="display: flex; gap: 12px;">
                @if ($generation->status === 'draft')
                    <form method="POST" action="{{ route('admin.ai-generations.approve', $generation) }}">
                        @csrf
                        <x-admin.button variant="primary" type="submit" style="background: var(--success); border-color: var(--success);">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Onayla
                        </x-admin.button>
                    </form>
                @endif
                <form method="POST" action="{{ route('admin.ai-generations.reject', $generation) }}">
                    @csrf
                    <x-admin.button variant="ghost" type="submit" style="color: var(--danger);">
                        <svg viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Reddet
                    </x-admin.button>
                </form>
                @if ($generation->status === 'approved' || $generation->status === 'publish_failed')
                    <form method="POST" action="{{ route('admin.ai-generations.publish', $generation) }}">
                        @csrf
                        <x-admin.button variant="primary" type="submit">
                            <svg viewBox="0 0 24 24"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg>
                            Yayinla
                        </x-admin.button>
                    </form>
                @endif
            </div>
        </x-admin.card>
    @endif

    @if ($generation->prompt)
        <x-admin.card eyebrow="Prompt" title="Kullanilan Prompt" style="margin-top: 24px;">
            <pre style="white-space: pre-wrap; font-size: 12px; line-height: 1.6; background: var(--bg-3); padding: 16px; border-radius: 6px; overflow-x: auto; max-height: 400px; overflow-y: auto;">{{ $generation->prompt }}</pre>
        </x-admin.card>
    @endif

    @if ($generation->full_prompt)
        <x-admin.card eyebrow="Full Prompt" title="Render Edilmis Prompt (AI'ye Gonderilen)" style="margin-top: 24px;">
            <pre style="white-space: pre-wrap; font-size: 12px; line-height: 1.6; background: var(--bg-3); padding: 16px; border-radius: 6px; overflow-x: auto; max-height: 500px; overflow-y: auto;">{{ $generation->full_prompt }}</pre>
        </x-admin.card>
    @endif

    @if ($generation->generated_news)
        <x-admin.card eyebrow="Sonuc" title="AI Ciktisi" style="margin-top: 24px;">
            <div style="white-space: pre-wrap; font-size: 14px; line-height: 1.8; background: var(--bg-3); padding: 20px; border-radius: 6px;">
                {{ $generation->generated_news }}
            </div>
        </x-admin.card>
    @endif

    @if ($generation->error)
        <x-admin.card eyebrow="Hata" title="Hata Detayi" style="margin-top: 24px;">
            <div style="padding: 12px; background: rgba(244,63,94,0.08); border-radius: 6px; border-left: 3px solid var(--danger); word-break: break-all;">
                {{ $generation->error }}
            </div>
        </x-admin.card>
    @endif

    @if ($generation->logs->isNotEmpty())
        <x-admin.card eyebrow="Loglar" title="Islem Loglari" style="margin-top: 24px;">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach ($generation->logs as $log)
                    <div style="padding: 10px 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid {{ $log->level === 'error' || $log->level === 'critical' ? 'var(--danger)' : ($log->level === 'warning' ? 'var(--warning, #f59e0b)' : 'var(--success)') }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span class="badge {{ $log->level === 'error' || $log->level === 'critical' ? 'danger' : ($log->level === 'warning' ? 'warning' : 'primary') }}">{{ $log->level }}</span>
                            <span style="font-size: 11px; color: var(--t-muted);">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                        <div style="font-size: 13px;">{{ $log->message }}</div>
                        @if ($log->context_json)
                            <pre style="margin-top: 6px; font-size: 11px; color: var(--t-muted); overflow-x: auto;">{{ json_encode($log->context_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif
@endsection
