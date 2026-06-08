@extends('admin.layouts.master')

@section('title', 'Yayin Kuyruk Detayi')
@section('active', 'publish-queue')
@section('crumbs', 'Yayin Yonetimi | Kuyruk | Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Kuyruk Detay"
        title="Yayin #{{ $item->id }}"
        subtitle="{{ $item->created_at->format('Y-m-d H:i') }}"
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.publish-queue.index')">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Kuyruga Don
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Meta" title="Yayin Bilgileri">
        <div class="body-text" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; line-height: 1.8;">
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">AI Generation</div>
                <div style="font-weight: 600;">
                    <a href="{{ route('admin.ai-generations.show', $item->aiGeneration) }}" style="color: var(--link);">
                        #{{ $item->ai_generation_id }}
                    </a>
                </div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Hesap</div>
                <div style="font-weight: 600;">{{ $item->publishAccount?->username ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Durum</div>
                @php
                    $statusTag = match($item->status) {
                        'pending' => 't-warning',
                        'processing' => 't-info',
                        'published' => 't-active',
                        'failed' => 't-danger',
                        default => 't-unavail',
                    };
                    $statusLabel = match($item->status) {
                        'pending' => 'Bekliyor',
                        'processing' => 'Isleniyor',
                        'published' => 'Yayinlandi',
                        'failed' => 'Basarisiz',
                        default => $item->status,
                    };
                @endphp
                <span class="tag {{ $statusTag }}">{{ $statusLabel }}</span>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Zamanlanma</div>
                <div style="font-weight: 600;">{{ $item->scheduled_at?->format('Y-m-d H:i:s') ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Baslangic</div>
                <div style="font-weight: 600;">{{ $item->started_at?->format('Y-m-d H:i:s') ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Bitis</div>
                <div style="font-weight: 600;">{{ $item->completed_at?->format('Y-m-d H:i:s') ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Tweet ID (X)</div>
                <div style="font-weight: 600;">{{ $item->tweet_id_x ?? '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Sure</div>
                <div style="font-weight: 600;">{{ $item->duration ? $item->duration . ' ms' : '—' }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Retry</div>
                <div style="font-weight: 600;">{{ $item->retry_count }}</div>
            </div>
        </div>

        @if ($item->error_message)
            <div style="margin-top: 16px; padding: 12px; background: rgba(244,63,94,0.08); border-radius: 6px; border-left: 3px solid var(--danger);">
                <div style="color: var(--danger); font-weight: 600; font-size: 12px; margin-bottom: 4px;">Hata</div>
                <div style="font-size: 13px; color: var(--danger);">{{ $item->error_message }}</div>
            </div>
        @endif
    </x-admin.card>

    @if ($item->status === 'failed')
        <x-admin.card eyebrow="Eylem" title="Yeniden Dene" style="margin-top: 24px;">
            <form method="POST" action="{{ route('admin.publish-queue.retry', $item) }}">
                @csrf
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/></svg>
                    Tekrar Dene
                </x-admin.button>
            </form>
        </x-admin.card>
    @endif

    @if ($item->aiGeneration)
        <x-admin.card eyebrow="Icerik" title="AI Uretimi" style="margin-top: 24px;">
            <div style="white-space: pre-wrap; font-size: 14px; line-height: 1.8; background: var(--bg-3); padding: 20px; border-radius: 6px;">
                {{ $item->aiGeneration->generated_news ?? '—' }}
            </div>
        </x-admin.card>
    @endif

    @if ($item->publishLogs->isNotEmpty())
        <x-admin.card eyebrow="Loglar" title="Islem Loglari" style="margin-top: 24px;">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach ($item->publishLogs as $log)
                    <div style="padding: 10px 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid {{ $log->status === 'failed' ? 'var(--danger)' : 'var(--success)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span class="badge {{ $log->status === 'failed' ? 'danger' : 'primary' }}">{{ $log->status }}</span>
                            <span style="font-size: 11px; color: var(--t-muted);">{{ $log->created_at->format('Y-m-d H:i:s') }}</span>
                        </div>
                        <div style="font-size: 13px;">Tweet ID: {{ $log->tweet_id_x ?? '—' }}</div>
                        @if ($log->error_message)
                            <div style="font-size: 12px; color: var(--danger); margin-top: 4px;">{{ $log->error_message }}</div>
                        @endif
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif
@endsection
