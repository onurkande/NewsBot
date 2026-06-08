@extends('admin.layouts.master')

@section('title', 'AI Ayarlari')
@section('active', 'ai-settings')
@section('crumbs', 'AI Yonetimi | AI Ayarlari')

@section('content')
    <x-admin.page-header
        eyebrow="Yapilandirma"
        title="AI Ayarlari"
        subtitle="AI provider secimi, API yapilandirmasi ve genel uretim ayarlarini buradan yonetin."
    />

    <x-admin.flash-message />

    <x-admin.card eyebrow="Provider" title="AI Provider Ayarlari">
        <form method="POST" action="{{ route('admin.ai-settings.update') }}" class="form-grid">
            @csrf
            @method('PUT')

            <div class="form-row">
                <x-admin.form.select
                    name="provider"
                    label="AI Provider"
                    :options="$providers"
                    :selected="old('provider', $settings->provider)"
                    required
                />
                <x-admin.form.select
                    name="active_prompt_id"
                    label="Aktif Prompt"
                    :options="$prompts->pluck('name', 'id')->prepend('-- Varsayilan --', '')->toArray()"
                    :selected="old('active_prompt_id', $settings->active_prompt_id)"
                />
            </div>

            <div class="form-row provider-fields gpt4free-fields" @if($settings->provider !== 'gpt4free') style="display:none;" @endif>
                <x-admin.form.input
                    name="model_name"
                    label="Model Adi (opsiyonel - bos birakilirsa otomatik)"
                    :value="old('model_name', $settings->model_name)"
                    placeholder="orn: DuckDuckGo"
                />
            </div>

            <div class="form-row provider-fields opencode-fields" @if($settings->provider !== 'opencode') style="display:none;" @endif>
                <x-admin.form.input
                    name="opencode_api_key"
                    label="OpenCode API Key"
                    type="password"
                    :value="old('opencode_api_key', $settings->opencode_api_key)"
                    placeholder="sk-..."
                />
                <x-admin.form.input
                    name="opencode_base_url"
                    label="OpenCode Base URL"
                    :value="old('opencode_base_url', $settings->opencode_base_url)"
                    placeholder="https://opencode.ai/zen/go/v1"
                />
            </div>

            <div class="form-row provider-fields opencode-fields" @if($settings->provider !== 'opencode') style="display:none;" @endif>
                <x-admin.form.input
                    name="opencode_model"
                    label="OpenCode Model"
                    :value="old('opencode_model', $settings->opencode_model)"
                    placeholder="deepseek-v4-flash"
                />
            </div>

            <div class="form-row">
                <x-admin.form.input
                    name="retry_count"
                    label="Tekrar Deneme Sayisi"
                    type="number"
                    :value="old('retry_count', $settings->retry_count)"
                    required
                />
                <x-admin.form.input
                    name="timeout"
                    label="Timeout (saniye)"
                    type="number"
                    :value="old('timeout', $settings->timeout)"
                    required
                />
            </div>

            <div class="form-row">
                <x-admin.form.input
                    name="concurrent_jobs"
                    label="Es Zamanli Job Sayisi"
                    type="number"
                    :value="old('concurrent_jobs', $settings->concurrent_jobs)"
                    required
                />
                <x-admin.form.checkbox
                    name="is_active"
                    label="AI uretimini aktif et"
                    :checked="old('is_active', $settings->is_active)"
                />
                <x-admin.form.checkbox
                    name="auto_approve"
                    label="AI uretimlerini otomatik onayla"
                    :checked="old('auto_approve', $settings->auto_approve)"
                />
                <x-admin.form.checkbox
                    name="auto_publish"
                    label="Onayli uretimleri otomatik yayin kuyruguna al"
                    :checked="old('auto_publish', $settings->auto_publish)"
                />
                <x-admin.form.input
                    name="publish_delay"
                    label="Yayin Gecikmesi (dakika)"
                    type="number"
                    :value="old('publish_delay', $settings->publish_delay)"
                />
            </div>

            <div class="form-actions" style="margin-top: 24px;">
                <x-admin.button variant="primary" type="submit">
                    <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Kaydet
                </x-admin.button>
            </div>
        </form>
    </x-admin.card>

    <x-admin.card eyebrow="Test" title="Baglanti Testi" style="margin-top: 24px;">
        <div class="form-grid" id="test-section">
            <div class="form-row">
                <x-admin.form.textarea
                    name="test_prompt"
                    label="Test Prompt"
                    :value="'Yapay zeka nedir, çok kısaca anlat.'"
                    rows="3"
                />
            </div>

            <div class="form-row">
                <x-admin.button variant="primary" id="test-btn" type="button">
                    <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    Baglantiyi Test Et
                </x-admin.button>
                <span id="test-spinner" style="display:none; color: var(--t-muted); font-size: 13px; padding: 10px;">Test yapiliyor...</span>
            </div>

            <div class="form-row">
                <div id="test-result" style="display:none; width:100%; padding: 14px; border-radius: 6px;"></div>
            </div>
        </div>
    </x-admin.card>

    <x-admin.card eyebrow="Provider Detaylari" title="Desteklenen Providerlar" style="margin-top: 24px;">
        <div class="body-text" style="line-height: 1.7;">
            <ul style="padding-left: 20px;">
                <li><strong>GPT4Free:</strong> Ucretsiz, Python g4f servisi uzerinden calisir. API key gerektirmez. Model havuzundan otomatik secer.</li>
                <li><strong>OpenCode:</strong> OpenCode AI API uzerinden calisir. OpenAI-compatible endpoint. API key, Base URL ve Model gerektirir.</li>
            </ul>
            <p style="margin-top: 12px; color: var(--t-muted);">
                Provider degisikligi tum yeni uretimleri etkiler. Mevcut uretimler etkilenmez.
            </p>
        </div>
    </x-admin.card>
@endsection

@push('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var providerSelect = document.querySelector('[name="provider"]');
    var gptFields = document.querySelector('.gpt4free-fields');
    var opencodeFields = document.querySelector('.opencode-fields');

    function toggleProviderFields() {
        var val = providerSelect.value;
        var allFields = document.querySelectorAll('.provider-fields');
        allFields.forEach(function (el) { el.style.display = 'none'; });

        if (val === 'gpt4free' && gptFields) gptFields.style.display = '';
        if (val === 'opencode' && opencodeFields) opencodeFields.style.display = '';
    }

    if (providerSelect) {
        providerSelect.addEventListener('change', toggleProviderFields);
    }

    var testBtn = document.getElementById('test-btn');
    if (testBtn) {
        testBtn.addEventListener('click', function () {
            var prompt = document.querySelector('[name="test_prompt"]').value.trim();
            if (!prompt) { alert('Lutfen bir test promptu girin.'); return; }

            var provider = providerSelect.value;
            var spinner = document.getElementById('test-spinner');
            var result = document.getElementById('test-result');
            var btn = document.getElementById('test-btn');

            btn.disabled = true;
            spinner.style.display = 'inline';
            result.style.display = 'none';

            fetch('{{ route("admin.ai-settings.test") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ provider: provider, prompt: prompt }),
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled = false;
                spinner.style.display = 'none';
                result.style.display = 'block';

                if (data.success) {
                    result.style.background = 'rgba(16,185,129,0.08)';
                    result.style.borderLeft = '3px solid var(--success)';
                    result.innerHTML = '<div style="color: var(--success); font-weight: 600; margin-bottom: 6px;">\u2713 Basarili</div>'
                        + '<div style="font-size: 13px; color: var(--t-muted);">Provider: ' + (data.provider || '—') + ' | Model: ' + (data.model || '—') + ' | Sure: ' + data.duration_ms + ' ms</div>'
                        + '<div style="font-size: 13px; margin-top: 8px; white-space: pre-wrap; line-height: 1.6;">' + (data.content || '') + '</div>';
                } else {
                    result.style.background = 'rgba(244,63,94,0.08)';
                    result.style.borderLeft = '3px solid var(--danger)';
                    result.innerHTML = '<div style="color: var(--danger); font-weight: 600; margin-bottom: 6px;">\u2717 Hata</div>'
                        + '<div style="font-size: 13px; color: var(--danger);">' + (data.error || 'Bilinmeyen hata') + '</div>'
                        + '<div style="font-size: 12px; color: var(--t-muted); margin-top: 4px;">Sure: ' + data.duration_ms + ' ms</div>';
                }
            })
            .catch(function (err) {
                btn.disabled = false;
                spinner.style.display = 'none';
                result.style.display = 'block';
                result.style.background = 'rgba(244,63,94,0.08)';
                result.style.borderLeft = '3px solid var(--danger)';
                result.innerHTML = '<div style="color: var(--danger); font-weight: 600;">\u2717 Hata</div>'
                    + '<div style="font-size: 13px; color: var(--danger);">Istek basarisiz: ' + (err.message || 'Ag hatasi') + '</div>';
            });
        });
    }
});
</script>
@endpush
