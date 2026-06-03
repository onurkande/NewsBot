<div class="form-grid">
    <div class="form-row">
        <x-admin.form.input
            name="name"
            label="Prompt Adi"
            :value="old('name', $prompt->name)"
            required
        />
        <x-admin.form.select
            name="source_category_id"
            label="Kategori"
            :options="$categories"
            :value="old('source_category_id', $prompt->source_category_id)"
        />
    </div>

    <div class="form-row">
        <x-admin.form.input
            name="version"
            label="Versiyon"
            type="number"
            :value="old('version', $prompt->version)"
            required
        />
        <x-admin.form.checkbox
            name="is_active"
            label="Aktif"
            :checked="old('is_active', $prompt->is_active)"
        />
    </div>

    <div class="form-row" style="margin-top: 16px;">
        <x-admin.form.textarea
            name="prompt_text"
            label="Prompt Metni"
            :value="old('prompt_text', $prompt->prompt_text)"
            required
            rows="15"
        />
    </div>

    <div class="form-row" style="margin-top: 0;">
        <div class="input-hint" style="width:100%; line-height: 1.7;">
            <strong>Kullanilabilir degiskenler:</strong>
            <ul style="margin: 4px 0 0 16px; padding: 0;">
                <li><code>{tweet_content}</code> - Tum tweet metinleri (zorunlu)</li>
                <li><code>{tweet_count}</code> - Secilen tweet sayisi</li>
                <li><code>{sources}</code> - Kaynak hesaplar (virgulle ayrilmis)</li>
                <li><code>{total_score}</code> - Toplam final puani</li>
                <li><code>{first_tweet}</code> - Ilk tweet metni</li>
            </ul>
            <p style="margin-top: 8px;">CIKTI FORMATI: <strong>BASLIK:</strong> ve <strong>ICERIK:</strong> seklinde cikti beklenir.</p>
        </div>
    </div>

    <div class="form-actions" style="margin-top: 24px;">
        <x-admin.button variant="primary" type="submit">
            <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Kaydet
        </x-admin.button>
        <x-admin.button variant="ghost" :href="route('admin.prompts.index')">
            Iptal
        </x-admin.button>
    </div>
</div>
