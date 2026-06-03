@extends('admin.layouts.master')

@section('title', 'Tweet Detay')
@section('active', 'raw-tweets')
@section('crumbs', 'Haber Toplama | Ham Tweetler | Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Tweet Detay"
        title="Tweet #{{ $rawTweet->id }}"
        subtitle="{{ $rawTweet->tweet_id }} | {{ $rawTweet->sourceAccount?->username ?? 'Bilinmeyen hesap' }}"
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.raw-tweets.index')">
                <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                Listeye Don
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    {{-- Tweet Ozeti --}}
    <x-admin.card eyebrow="Ozeti" title="Tweet Bilgileri">
        <div class="form-grid">
            <div class="field">
                <div class="field-label">ID</div>
                <div class="data-cell-mono">#{{ $rawTweet->id }}</div>
            </div>
            <div class="field">
                <div class="field-label">Tweet ID</div>
                <div class="data-cell-mono">{{ $rawTweet->tweet_id }}</div>
            </div>
            @if ($rawTweet->tweet_url)
                <div class="field">
                    <div class="field-label">Tweet URL</div>
                    <a href="{{ $rawTweet->tweet_url }}" target="_blank" rel="noopener noreferrer" class="data-cell-mono" style="color: var(--link); word-break: break-all;">
                        {{ $rawTweet->tweet_url }}
                    </a>
                </div>
            @endif
            <div class="field">
                <div class="field-label">Tweet Tarihi</div>
                <div class="data-cell-mono">{{ $rawTweet->tweeted_at?->format('Y-m-d H:i:s') ?: '-' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Cekilme Tarihi</div>
                <div class="data-cell-mono">{{ $rawTweet->fetched_at?->format('Y-m-d H:i:s') ?: '-' }}</div>
            </div>
        </div>

        <div style="margin-top: 20px;">
            <div class="field-label" style="margin-bottom: 8px;">Tweet Icerigi</div>
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; font-size: 14px; line-height: 1.6; white-space: pre-wrap; word-break: break-word;">{{ $rawTweet->tweet_text }}</div>
        </div>
    </x-admin.card>

    {{-- Kaynak Bilgileri --}}
    <x-admin.card eyebrow="Kaynak" title="Kaynak Hesap Bilgileri" style="margin-top: 24px;">
        <div class="form-grid">
            <div class="field">
                <div class="field-label">Kullanici Adi</div>
                <div style="font-weight: 600;">{{ $rawTweet->sourceAccount?->username ?? '-' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Gorunen Ad</div>
                <div>{{ $rawTweet->sourceAccount?->display_name ?? '-' }}</div>
            </div>
            <div class="field">
                <div class="field-label">Kategori</div>
                <div>{{ $rawTweet->sourceAccount?->category?->name ?? '-' }}</div>
            </div>
            @if ($rawTweet->sourceAccount)
                <div class="field">
                    <div class="field-label">Hesap Detay</div>
                    <a href="{{ route('admin.source-accounts.edit', $rawTweet->sourceAccount) }}" style="color: var(--link); font-weight: 600;">
                        Hesabi Goruntule &rarr;
                    </a>
                </div>
            @endif
        </div>

        @if ($rawTweet->normalizedText)
            <div style="margin-top: 20px;">
                <div class="field-label" style="margin-bottom: 8px;">Normalize Edilmis Metin</div>
                <div style="padding: 12px; background: var(--bg-3); border-radius: 6px; font-size: 13px; line-height: 1.5; white-space: pre-wrap; word-break: break-word;">{{ $rawTweet->normalizedText->normalized_text }}</div>
            </div>
        @endif

        @if ($rawTweet->storyClusterItem?->storyCluster)
            <div style="margin-top: 16px;">
                <div class="field-label" style="margin-bottom: 8px;">Story Cluster</div>
                <a href="{{ route('admin.story-clusters.show', $rawTweet->storyClusterItem->storyCluster) }}" style="color: var(--link); font-weight: 600;">
                    {{ $rawTweet->storyClusterItem->storyCluster->title }} &rarr;
                </a>
            </div>
        @endif
    </x-admin.card>

    {{-- Etkilesim Bilgileri --}}
    <x-admin.card eyebrow="Etkilesim" title="Etkilesim Istatistikleri" style="margin-top: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px;">
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; text-align: center;">
                <div style="font-size: 12px; color: var(--t-muted); margin-bottom: 4px;">Begeni</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--success);">{{ number_format($rawTweet->like_count) }}</div>
            </div>
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; text-align: center;">
                <div style="font-size: 12px; color: var(--t-muted); margin-bottom: 4px;">Retweet</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--primary);">{{ number_format($rawTweet->retweet_count) }}</div>
            </div>
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; text-align: center;">
                <div style="font-size: 12px; color: var(--t-muted); margin-bottom: 4px;">Yanit</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--info, #3b82f6);">{{ number_format($rawTweet->reply_count) }}</div>
            </div>
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; text-align: center;">
                <div style="font-size: 12px; color: var(--t-muted); margin-bottom: 4px;">Alinti</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--warning, #f59e0b);">{{ number_format($rawTweet->quote_count) }}</div>
            </div>
            <div style="padding: 16px; background: var(--bg-3); border-radius: 6px; text-align: center;">
                <div style="font-size: 12px; color: var(--t-muted); margin-bottom: 4px;">Goruntulenme</div>
                <div style="font-size: 24px; font-weight: 700; color: var(--t-base);">{{ number_format($rawTweet->view_count) }}</div>
            </div>
        </div>
    </x-admin.card>

    {{-- Isleme Durumu --}}
    <x-admin.card eyebrow="Durum" title="Isleme Durumu" style="margin-top: 24px;">
        <div class="form-grid">
            <div class="field">
                <div class="field-label">Islenme Durumu</div>
                <span class="tag {{ $rawTweet->is_processed ? 't-active' : 't-unavail' }}">
                    {{ $rawTweet->is_processed ? 'Islendi' : 'Bekliyor' }}
                </span>
            </div>
            <div class="field">
                <div class="field-label">Havuz Secimi</div>
                <span class="tag {{ $rawTweet->selected_for_pool ? 't-active' : 't-unavail' }}">
                    {{ $rawTweet->selected_for_pool ? 'Secildi' : 'Secilmedi' }}
                </span>
            </div>
            @if ($rawTweet->selected_at)
                <div class="field">
                    <div class="field-label">Havuz Secim Tarihi</div>
                    <div class="data-cell-mono">{{ $rawTweet->selected_at->format('Y-m-d H:i:s') }}</div>
                </div>
            @endif
            <div class="field">
                <div class="field-label">AI Gonderim</div>
                <span class="tag {{ $rawTweet->selected_for_ai ? 't-info' : 't-unavail' }}">
                    {{ $rawTweet->selected_for_ai ? 'Gonderildi' : 'Gonderilmedi' }}
                </span>
            </div>
            @if ($rawTweet->ai_sent_at)
                <div class="field">
                    <div class="field-label">AI Gonderim Tarihi</div>
                    <div class="data-cell-mono">{{ $rawTweet->ai_sent_at->format('Y-m-d H:i:s') }}</div>
                </div>
            @endif
        </div>

        <div style="margin-top: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div>
                <div class="field-label" style="margin-bottom: 4px;">Olusturulma</div>
                <div class="data-cell-mono">{{ $rawTweet->created_at->format('Y-m-d H:i:s') }}</div>
            </div>
            <div>
                <div class="field-label" style="margin-bottom: 4px;">Guncelleme</div>
                <div class="data-cell-mono">{{ $rawTweet->updated_at->format('Y-m-d H:i:s') }}</div>
            </div>
        </div>
    </x-admin.card>

    {{-- Havuz Bilgileri --}}
    @if ($rawTweet->poolBatchItems->isNotEmpty())
        <x-admin.card eyebrow="Havuz" title="Havuz Bilgileri" style="margin-top: 24px;">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($rawTweet->poolBatchItems as $poolItem)
                    <div style="padding: 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid {{ $poolItem->is_selected ? 'var(--success)' : 'var(--danger)' }};">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div>
                                <span style="font-weight: 600;">Batch #{{ $poolItem->poolBatch->batch_no }}</span>
                                <span class="tag {{ $poolItem->is_selected ? 't-active' : 't-unavail' }}" style="margin-left: 8px;">
                                    {{ $poolItem->is_selected ? 'Secildi' : 'Secilmedi' }}
                                </span>
                            </div>
                            <a href="{{ route('admin.pool-history.show', $poolItem->poolBatch) }}" style="color: var(--link); font-size: 13px;">
                                Batch Detay &rarr;
                            </a>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; font-size: 13px;">
                            <div>
                                <span style="color: var(--t-muted);">Oncelik:</span>
                                <span style="font-weight: 600; margin-left: 4px;">{{ number_format($poolItem->priority_score, 2) }}</span>
                            </div>
                            <div>
                                <span style="color: var(--t-muted);">Etkilesim:</span>
                                <span style="font-weight: 600; margin-left: 4px;">{{ number_format($poolItem->engagement_score, 2) }}</span>
                            </div>
                            <div>
                                <span style="color: var(--t-muted);">Final:</span>
                                <span style="font-weight: 600; margin-left: 4px;">{{ number_format($poolItem->final_score, 2) }}</span>
                            </div>
                            <div>
                                <span style="color: var(--t-muted);">Sira:</span>
                                <span style="font-weight: 600; margin-left: 4px;">{{ $poolItem->rank }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif

    {{-- AI Bilgileri --}}
    @if ($rawTweet->aiGenerations->isNotEmpty())
        <x-admin.card eyebrow="AI" title="AI Uretim Bilgileri" style="margin-top: 24px;">
            <div style="display: flex; flex-direction: column; gap: 12px;">
                @foreach ($rawTweet->aiGenerations as $generation)
                    <div style="padding: 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid var(--primary);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                            <div>
                                <span style="font-weight: 600;">Uretim #{{ $generation->id }}</span>
                                @php
                                    $statusTag = match($generation->status) {
                                        'draft' => 't-unavail',
                                        'approved' => 't-info',
                                        'rejected' => 't-danger',
                                        'published' => 't-active',
                                        default => 't-unavail'
                                    };
                                @endphp
                                <span class="tag {{ $statusTag }}" style="margin-left: 8px;">
                                    {{ $generation->status }}
                                </span>
                            </div>
                            <a href="{{ route('admin.ai-generations.show', $generation) }}" style="color: var(--link); font-size: 13px;">
                                Uretim Detay &rarr;
                            </a>
                        </div>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 12px; font-size: 13px;">
                            <div>
                                <span style="color: var(--t-muted);">Model:</span>
                                <span style="font-weight: 600; margin-left: 4px;">{{ $generation->model ?? '-' }}</span>
                            </div>
                            <div>
                                <span style="color: var(--t-muted);">Batch:</span>
                                <span style="font-weight: 600; margin-left: 4px;">{{ $generation->aiQueue?->batch_no ?? '-' }}</span>
                            </div>
                            @if ($generation->generated_at)
                                <div>
                                    <span style="color: var(--t-muted);">Tarih:</span>
                                    <span style="font-weight: 600; margin-left: 4px;">{{ $generation->generated_at->format('Y-m-d H:i') }}</span>
                                </div>
                            @endif
                            @if ($generation->duration)
                                <div>
                                    <span style="color: var(--t-muted);">Sure:</span>
                                    <span style="font-weight: 600; margin-left: 4px;">{{ $generation->duration }} ms</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </x-admin.card>
    @endif

    {{-- Duplicate Bilgileri --}}
    @if ($rawTweet->duplicateChecks->isNotEmpty() || $rawTweet->matchedDuplicates->isNotEmpty())
        <x-admin.card eyebrow="Duplicate" title="Tekrar Kontrol Bilgileri" style="margin-top: 24px;">
            @if ($rawTweet->duplicateChecks->isNotEmpty())
                <div style="margin-bottom: 16px;">
                    <div class="field-label" style="margin-bottom: 8px;">Bu Tweetin Yaptigi Eslesmeler</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($rawTweet->duplicateChecks as $dup)
                            <div style="padding: 10px 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid {{ $dup->is_duplicate ? 'var(--danger)' : 'var(--success)' }};">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                    <div>
                                        <span class="tag {{ $dup->is_duplicate ? 't-danger' : 't-active' }}" style="margin-right: 8px;">
                                            {{ $dup->is_duplicate ? 'Duplicate' : 'Unique' }}
                                        </span>
                                        <span style="color: var(--t-muted);">Tip:</span>
                                        <span style="font-weight: 600; margin-left: 4px;">{{ $dup->match_type }}</span>
                                        @if ($dup->similarity_score)
                                            <span style="color: var(--t-muted); margin-left: 8px;">Benzerlik:</span>
                                            <span style="font-weight: 600; margin-left: 4px;">{{ $dup->similarity_score }}</span>
                                        @endif
                                    </div>
                                    @if ($dup->matchedTweet)
                                        <a href="{{ route('admin.raw-tweets.show', $dup->matchedTweet) }}" style="color: var(--link); font-size: 12px;">
                                            Eslesen Tweet &rarr;
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($rawTweet->matchedDuplicates->isNotEmpty())
                <div>
                    <div class="field-label" style="margin-bottom: 8px;">Bu Tweetle Eslesen Diger Tweetler</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach ($rawTweet->matchedDuplicates as $dup)
                            <div style="padding: 10px 14px; background: var(--bg-3); border-radius: 6px; border-left: 3px solid var(--danger);">
                                <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px;">
                                    <div>
                                        <span style="color: var(--t-muted);">Kaynak Tweet ID:</span>
                                        <span class="data-cell-mono" style="margin-left: 4px;">{{ $dup->raw_tweet_id }}</span>
                                        <span style="color: var(--t-muted); margin-left: 8px;">Tip:</span>
                                        <span style="font-weight: 600; margin-left: 4px;">{{ $dup->match_type }}</span>
                                    </div>
                                    <a href="{{ route('admin.raw-tweets.show', $dup->rawTweet) }}" style="color: var(--link); font-size: 12px;">
                                        Kaynak Tweet &rarr;
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </x-admin.card>
    @endif

    {{-- Ham Veri --}}
    <x-admin.card eyebrow="Ham Veri" title="Raw Payload" style="margin-top: 24px;">
        @if ($rawTweet->raw_payload)
            <pre style="white-space: pre-wrap; font-size: 12px; line-height: 1.6; background: var(--bg-3); padding: 16px; border-radius: 6px; overflow-x: auto; max-height: 600px; overflow-y: auto; font-family: ui-monospace, SFMono-Regular, 'SF Mono', Menlo, Consolas, 'Liberation Mono', monospace;">{{ json_encode($rawTweet->raw_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
        @else
            <x-admin.empty-state
                title="Ham veri yok"
                description="Bu tweet icin raw payload kaydedilmemis."
            />
        @endif
    </x-admin.card>
@endsection
