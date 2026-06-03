@extends('admin.layouts.master')

@section('title', 'AI Kuyrugu Detay')
@section('active', 'ai-queue')
@section('crumbs', 'AI Yonetimi | AI Kuyrugu | Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Kuyruk Detay"
        title="{{ $queue->batch_no }}"
        subtitle="AI kuyruk kaydi ve iliskili uretim bilgileri."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.ai-queue.index')">
                <svg viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
                Kuyruga Don
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Kuyruk" title="Kuyruk Bilgileri">
        <div class="body-text" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; line-height: 1.8;">
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">ID</div>
                <div style="font-weight: 600;">{{ $queue->id }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Batch No</div>
                <div style="font-weight: 600;">{{ $queue->batch_no }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Tweet Sayisi</div>
                <div style="font-weight: 600;">{{ $queue->tweet_count }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Hikaye Puani</div>
                <div style="font-weight: 600;">{{ number_format($queue->story_score, 2) }}</div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Durum</div>
                <div>
                    @php
                        $statusTag = match($queue->status) {'pending' => 't-unavail', 'processing' => 't-info', 'completed' => 't-active', 'failed' => 't-danger', default => 't-unavail'};
                    @endphp
                    <span class="tag {{ $statusTag }}">{{ $queue->status }}</span>
                </div>
            </div>
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Olusturma</div>
                <div style="font-weight: 600;">{{ $queue->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
            @if ($queue->started_at)
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Baslangic</div>
                <div style="font-weight: 600;">{{ $queue->started_at->format('Y-m-d H:i:s') }}</div>
            </div>
            @endif
            @if ($queue->completed_at)
            <div>
                <div style="color: var(--t-muted); font-size: 12px;">Tamamlanma</div>
                <div style="font-weight: 600;">{{ $queue->completed_at->format('Y-m-d H:i:s') }}</div>
            </div>
            @endif
        </div>

        @if ($queue->error_message)
            <div style="margin-top: 16px; padding: 12px; background: rgba(244,63,94,0.08); border-radius: 6px; border-left: 3px solid var(--danger);">
                <div style="color: var(--t-muted); font-size: 12px;">Hata Mesaji</div>
                <div style="font-weight: 600; color: var(--danger); word-break: break-all;">{{ $queue->error_message }}</div>
            </div>
        @endif
    </x-admin.card>

    @if ($queue->generation)
        <x-admin.card eyebrow="Uretim" title="Iliskili AI Uretimi" style="margin-top: 24px;">
            <a href="{{ route('admin.ai-generations.show', $queue->generation) }}" class="btn btn--ghost">
                <svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                Uretim Detayina Git
            </a>
        </x-admin.card>
    @endif

    @if ($queue->logs->isNotEmpty())
        <x-admin.card eyebrow="Loglar" title="Islem Loglari" style="margin-top: 24px;">
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach ($queue->logs as $log)
                    <div style="padding: 10px 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid {{ $log->level === 'error' ? 'var(--danger)' : ($log->level === 'warning' ? 'var(--warning, #f59e0b)' : 'var(--success)') }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                            <span class="badge {{ $log->level === 'error' ? 'danger' : ($log->level === 'warning' ? 'warning' : 'primary') }}">{{ $log->level }}</span>
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
