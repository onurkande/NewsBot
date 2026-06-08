@extends('admin.layouts.master')

@section('title', 'Yayin Ayarlari')
@section('active', 'publish-settings')
@section('crumbs', 'Yayin Yonetimi | Ayarlar')

@section('content')
    <x-admin.page-header
        eyebrow="Yapilandirma"
        title="Yayin Ayarlari"
        subtitle="Yayin limitleri, gecikme, gece modu, warmup ve expiration ayarlarini buradan yonetin."
    />

    <x-admin.flash-message />

    <form method="POST" action="{{ route('admin.publish-settings.update') }}">
        @csrf
        @method('PUT')

        <x-admin.card eyebrow="Limitler" title="Yayin Limitleri">
            <div class="form-grid">
                <div class="form-row">
                    <x-admin.form.input
                        name="daily_post_limit"
                        label="Gunluk Paylasim Limiti"
                        type="number"
                        :value="old('daily_post_limit', $settings->daily_post_limit)"
                        required
                    />
                    <x-admin.form.input
                        name="hourly_post_limit"
                        label="Saatlik Paylasim Limiti"
                        type="number"
                        :value="old('hourly_post_limit', $settings->hourly_post_limit)"
                        required
                    />
                </div>

                <div class="form-row">
                    <x-admin.form.input
                        name="min_delay_minutes"
                        label="Minimum Gecikme (dk)"
                        type="number"
                        :value="old('min_delay_minutes', $settings->min_delay_minutes)"
                        required
                    />
                    <x-admin.form.input
                        name="max_delay_minutes"
                        label="Maksimum Gecikme (dk)"
                        type="number"
                        :value="old('max_delay_minutes', $settings->max_delay_minutes)"
                        required
                    />
                </div>

                <div class="form-row">
                    <x-admin.form.input
                        name="publish_start_hour"
                        label="Yayin Baslangic Saati (0-23)"
                        type="number"
                        :value="old('publish_start_hour', $settings->publish_start_hour)"
                        required
                    />
                    <x-admin.form.input
                        name="publish_end_hour"
                        label="Yayin Bitis Saati (0-24)"
                        type="number"
                        :value="old('publish_end_hour', $settings->publish_end_hour)"
                        required
                    />
                </div>

                <div class="form-row">
                    <x-admin.form.input
                        name="min_gap_minutes"
                        label="Iki Paylasim Arasi Minimum Sure (dk)"
                        type="number"
                        :value="old('min_gap_minutes', $settings->min_gap_minutes)"
                        required
                    />
                    <x-admin.form.input
                        name="publish_expiration_hours"
                        label="Yayin Gecerlilik Suresi (Saat)"
                        type="number"
                        :value="old('publish_expiration_hours', $settings->publish_expiration_hours)"
                        required
                    />
                </div>

                <div class="form-row">
                    <x-admin.form.checkbox
                        name="auto_publish_enabled"
                        label="Otomatik yayini aktif et"
                        :checked="old('auto_publish_enabled', $settings->auto_publish_enabled)"
                    />
                    <x-admin.form.checkbox
                        name="auto_scale_enabled"
                        label="Otomatik scale aktif et"
                        :checked="old('auto_scale_enabled', $settings->auto_scale_enabled)"
                    />
                </div>

                <div class="form-row">
                    <x-admin.form.checkbox
                        name="follower_scale_enabled"
                        label="Takipci bazli scale aktif et"
                        :checked="old('follower_scale_enabled', $settings->follower_scale_enabled)"
                    />
                    <x-admin.form.checkbox
                        name="engagement_scale_enabled"
                        label="Etkilesim bazli scale aktif et"
                        :checked="old('engagement_scale_enabled', $settings->engagement_scale_enabled)"
                    />
                </div>

                <div style="color: var(--t-muted); font-size: 13px; margin-top: 8px;">
                    Onaylanan (approved) icerikler <strong>publish_expiration_hours</strong> suresi icinde yayinlanamazsa <strong>expired</strong> durumuna gecer ve bir daha yayinlanmaz. Varsayilan: 6 saat. 0 = sinirsiz.
                </div>
            </div>
        </x-admin.card>

        <x-admin.card eyebrow="Warmup" title="Warmup Mode" style="margin-top: 24px;">
            <div class="form-grid">
                <div class="form-row">
                    <x-admin.form.checkbox
                        name="warmup_mode_enabled"
                        label="Warmup mode aktif et"
                        :checked="old('warmup_mode_enabled', $settings->warmup_mode_enabled)"
                    />
                </div>

                <div class="form-row">
                    <x-admin.form.input
                        name="warmup_0_30_daily_limit"
                        label="0-30 Gun Gunluk Limit"
                        type="number"
                        :value="old('warmup_0_30_daily_limit', $settings->warmup_0_30_daily_limit)"
                        required
                    />
                    <x-admin.form.input
                        name="warmup_30_60_daily_limit"
                        label="30-60 Gun Gunluk Limit"
                        type="number"
                        :value="old('warmup_30_60_daily_limit', $settings->warmup_30_60_daily_limit)"
                        required
                    />
                    <x-admin.form.input
                        name="warmup_60_plus_daily_limit"
                        label="60+ Gun Gunluk Limit"
                        type="number"
                        :value="old('warmup_60_plus_daily_limit', $settings->warmup_60_plus_daily_limit)"
                        required
                    />
                </div>
            </div>
        </x-admin.card>

        <div class="form-actions" style="margin-top: 24px;">
            <x-admin.button variant="primary" type="submit">
                <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                Kaydet
            </x-admin.button>
        </div>
    </form>
@endsection
