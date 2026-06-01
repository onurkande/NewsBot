@extends('admin.layouts.master')

@section('title', 'Havuz Ayarlari')
@section('active', 'pool-settings')
@section('crumbs', 'Havuz | Havuz Ayarlari')

@section('content')
    <x-admin.page-header
        eyebrow="Yapilandirma"
        title="Havuz Ayarlari"
        subtitle="Tweet havuzu secim algoritmasinin calisma araliklarini ve secim kriterlerini buradan yonetin."
    />

    <x-admin.flash-message />

    <x-admin.card eyebrow="Genel" title="Calisma Araliklari">
        <form method="POST" action="{{ route('admin.pool-settings.update') }}" class="form-grid">
            @csrf
            @method('PUT')

            <div class="form-row">
                <x-admin.form.input
                    name="tweet_window_min"
                    label="Minimum Tweet Toplama Araligi (dakika)"
                    type="number"
                    :value="old('tweet_window_min', $settings->tweet_window_min)"
                    required
                />
                <x-admin.form.input
                    name="tweet_window_max"
                    label="Maksimum Tweet Toplama Araligi (dakika)"
                    type="number"
                    :value="old('tweet_window_max', $settings->tweet_window_max)"
                    required
                />
            </div>

            <div class="form-row">
                <x-admin.form.input
                    name="selection_interval_min"
                    label="Minimum Secim Calisma Araligi (dakika)"
                    type="number"
                    :value="old('selection_interval_min', $settings->selection_interval_min)"
                    required
                />
                <x-admin.form.input
                    name="selection_interval_max"
                    label="Maksimum Secim Calisma Araligi (dakika)"
                    type="number"
                    :value="old('selection_interval_max', $settings->selection_interval_max)"
                    required
                />
            </div>

            <div class="form-row">
                <x-admin.form.input
                    name="tweet_count_min"
                    label="Minimum Secilecek Tweet Sayisi"
                    type="number"
                    :value="old('tweet_count_min', $settings->tweet_count_min)"
                    required
                />
                <x-admin.form.input
                    name="tweet_count_max"
                    label="Maksimum Secilecek Tweet Sayisi"
                    type="number"
                    :value="old('tweet_count_max', $settings->tweet_count_max)"
                    required
                />
            </div>

            <div class="form-row">
                <x-admin.form.checkbox
                    name="is_active"
                    label="Havuz secimini aktif et"
                    :checked="old('is_active', $settings->is_active)"
                />
            </div>

            @if ($settings->next_run_at)
                <div class="form-row" style="margin-top: 8px;">
                    <div class="input-hint">
                        Bir sonraki calisma zamani:
                        <strong>{{ $settings->next_run_at->format('Y-m-d H:i:s') }}</strong>
                        ({{ $settings->next_run_at->diffForHumans() }})
                    </div>
                </div>
            @endif

            <div class="form-actions" style="margin-top: 24px;">
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Kaydet
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card eyebrow="Algoritma" title="Puanlama Mantigi" style="margin-top: 24px;">
        <div class="body-text" style="line-height: 1.7;">
            <p><strong>Final Puan = (Oncelik Puani x 0.40) + (Etkilesim Puani x 0.60)</strong></p>
            <ul style="margin-top: 12px; padding-left: 20px;">
                <li><strong>Oncelik Puani:</strong> Kaynak hesabin <em>priority_score</em> degeridir (0-100).</li>
                <li><strong>Etkilesim Puani:</strong> Her tweetin ham etkilesimi asagidaki formulle hesaplanir ve en yuksek deger normalize edilerek 0-100 skalasina cekilir.</li>
                <li style="margin-top: 8px;"><code>likes + retweets*2 + replies*1.5 + quotes*1.2 + views*0.001</code></li>
            </ul>
            <p style="margin-top: 12px; color: var(--t-muted);">
                Sistem her calistiginda rastgele bir tweet araligi ve rastgele bir secim sayisi belirler.
                Secilen tweetler bir daha asla havuza aday olamaz.
            </p>
        </div>
    </x-admin.card>
@endsection
