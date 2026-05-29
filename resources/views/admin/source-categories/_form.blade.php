@csrf

<div class="form-grid">
    <x-admin.form.input
        name="name"
        label="Kategori adı"
        :value="old('name', $category->name)"
        placeholder="Kategori adı"
        required
    />

    <x-admin.form.input
        name="slug"
        label="Slug"
        :value="old('slug', $category->slug)"
        placeholder="Boş bırakılırsa otomatik üretilir"
    />

    <x-admin.form.textarea
        class="span-2"
        name="description"
        label="Açıklama"
        :value="old('description', $category->description)"
        placeholder="Kategori açıklaması"
    />
</div>

<div class="form-actions">
    <x-admin.button variant="secondary" :href="route('admin.source-categories.index')">Vazgeç</x-admin.button>

    <div class="spacer"></div>

    <x-admin.button type="submit" variant="primary">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" /><path d="M17 21v-8H7v8" /><path d="M7 3v5h8" /></svg>
        Kaydet
    </x-admin.button>
</div>
