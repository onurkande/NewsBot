@extends('admin.layouts.master')

@section('title', 'Batch Detay')
@section('active', 'pool-history')
@section('crumbs', 'Havuz | Havuz Gecmisi | Batch Detay')

@section('content')
    <x-admin.page-header
        eyebrow="Batch: {{ $batch->batch_no }}"
        title="Secim Detayi"
        subtitle="{{ $batch->started_at->format('Y-m-d H:i') }} tarihli havuz secim dongusunun detaylari."
    >
        <x-slot:actions>
            <x-admin.button variant="ghost" :href="route('admin.pool-history.index')">
                <svg viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                Geri
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <x-admin.flash-message />

    <x-admin.card eyebrow="Ozeti" title="Batch Bilgileri">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 16px;">
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Batch No</div>
                <div class="data-cell-mono" style="font-weight: 600;">{{ $batch->batch_no }}</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Calisma Tarihi</div>
                <div style="font-weight: 600;">{{ $batch->started_at->format('Y-m-d H:i:s') }}</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Tweet Araligi</div>
                <div style="font-weight: 600;">{{ $batch->tweet_window_minutes }} dk</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Toplam Aday</div>
                <div style="font-weight: 600;">{{ $batch->candidate_count }}</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Secilen</div>
                <div style="font-weight: 600;">{{ $batch->selected_count }}</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Bekleme Suresi</div>
                <div style="font-weight: 600;">{{ $batch->wait_duration_minutes }} dk</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Sonraki Calisma</div>
                <div style="font-weight: 600;">{{ $batch->next_run_at->format('Y-m-d H:i:s') }}</div>
            </div>
            <div>
                <div class="input-hint" style="margin-bottom: 4px;">Durum</div>
                <div>
                    <span class="tag {{ $batch->status === 'completed' ? 't-active' : 't-unavail' }}">
                        {{ $batch->status === 'completed' ? 'Tamamlandi' : 'Basarisiz' }}
                    </span>
                </div>
            </div>
        </div>

        @if ($batch->error_message)
            <div style="margin-top: 16px; padding: 12px; border-radius: 8px; background: rgba(244, 63, 94, 0.08); color: var(--danger); font-size: 14px;">
                <strong>Hata:</strong> {{ $batch->error_message }}
            </div>
        @endif
    </x-admin.card>

    <x-admin.card eyebrow="Secilenler" title="Havuza Secilen Tweetler" style="margin-top: 24px;">
        <div style="overflow-x: auto; margin: 0 -22px;">
            <table class="data-table" style="margin: 0 22px; min-width: 900px;">
                <thead>
                    <tr>
                        <th>Tweet ID</th>
                        <th>Kaynak</th>
                        <th>Icerik</th>
                        <th>Oncelik</th>
                        <th>Etkilesim</th>
                        <th>Final</th>
                        <th>Sira</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($selectedItems as $item)
                        <tr style="background: rgba(16, 185, 129, 0.06);">
                            <td class="data-cell-mono" style="color: var(--success);">{{ $item->rawTweet->tweet_id }}</td>
                            <td>{{ $item->rawTweet->sourceAccount?->username ?? '—' }}</td>
                            <td>
                                <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $item->rawTweet->tweet_text }}
                                </div>
                            </td>
                            <td><span class="badge primary">{{ number_format($item->priority_score, 2) }}</span></td>
                            <td><span class="badge primary">{{ number_format($item->engagement_score, 2) }}</span></td>
                            <td><span class="badge success">{{ number_format($item->final_score, 2) }}</span></td>
                            <td><span class="data-cell-mono">{{ $item->rank }}</span></td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <a class="btn--icon" href="{{ route('admin.raw-tweets.show', $item->rawTweet) }}" aria-label="Detay">
                                        <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state
                                    title="Secilen tweet yok"
                                    description="Bu dongude hic tweet secilmemis."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>

    <x-admin.card eyebrow="Secilmeyenler" title="Degerlendirilen Ama Secilmeyen Tweetler" style="margin-top: 24px;">
        <div style="overflow-x: auto; margin: 0 -22px;">
            <table class="data-table" style="margin: 0 22px; min-width: 900px;">
                <thead>
                    <tr>
                        <th>Tweet ID</th>
                        <th>Kaynak</th>
                        <th>Icerik</th>
                        <th>Oncelik</th>
                        <th>Etkilesim</th>
                        <th>Final</th>
                        <th>Sira</th>
                        <th style="text-align: right">Islemler</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($notSelectedItems as $item)
                        <tr style="background: rgba(244, 63, 94, 0.04);">
                            <td class="data-cell-mono" style="color: var(--danger);">{{ $item->rawTweet->tweet_id }}</td>
                            <td>{{ $item->rawTweet->sourceAccount?->username ?? '—' }}</td>
                            <td>
                                <div style="max-width: 400px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $item->rawTweet->tweet_text }}
                                </div>
                            </td>
                            <td><span class="badge primary">{{ number_format($item->priority_score, 2) }}</span></td>
                            <td><span class="badge primary">{{ number_format($item->engagement_score, 2) }}</span></td>
                            <td><span class="badge primary">{{ number_format($item->final_score, 2) }}</span></td>
                            <td><span class="data-cell-mono">{{ $item->rank }}</span></td>
                            <td>
                                <div class="data-cell-actions" style="justify-content: flex-end">
                                    <a class="btn--icon" href="{{ route('admin.raw-tweets.show', $item->rawTweet) }}" aria-label="Detay">
                                        <svg viewBox="0 0 24 24"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z" /><circle cx="12" cy="12" r="3" /></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-admin.empty-state
                                    title="Secilmeyen tweet yok"
                                    description="Tum aday tweetler secilmis."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-admin.card>
@endsection
