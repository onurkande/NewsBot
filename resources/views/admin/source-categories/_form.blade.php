@csrf

<div class="form-grid">
    <div class="field">
        <label class="field-label" for="name">Kategori adi <span class="req">*</span></label>
        <input class="input @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $category->name) }}" required>
        @error('name') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label class="field-label" for="slug">Slug</label>
        <input class="input @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $category->slug) }}" placeholder="bos birakilirsa otomatik uretilir">
        @error('slug') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field span-2">
        <label class="field-label" for="description">Aciklama</label>
        <textarea class="textarea @error('description') is-invalid @enderror" id="description" name="description">{{ old('description', $category->description) }}</textarea>
        @error('description') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-actions">
    <a class="btn btn--secondary" href="{{ route('admin.source-categories.index') }}">Vazgec</a>
    <div class="spacer"></div>
    <button class="btn btn--primary" type="submit">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" /><path d="M17 21v-8H7v8" /><path d="M7 3v5h8" /></svg>
        Kaydet
    </button>
</div>
