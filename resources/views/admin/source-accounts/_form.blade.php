@csrf

<div class="form-grid">
    <div class="field">
        <label class="field-label" for="username">X kullanici adi <span class="req">*</span></label>
        <div class="input-group">
            <span class="addon">@</span>
            <input class="input @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $account->username) }}" required>
        </div>
        @error('username') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label class="field-label" for="display_name">Gorunen ad</label>
        <input class="input @error('display_name') is-invalid @enderror" id="display_name" name="display_name" value="{{ old('display_name', $account->display_name) }}">
        @error('display_name') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label class="field-label" for="category_id">Kategori</label>
        <select class="select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
            <option value="">Kategori secilmedi</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) old('category_id', $account->category_id) === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
        @error('category_id') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label class="field-label" for="check_interval_minutes">Kontrol araligi <span class="req">*</span></label>
        <input class="input @error('check_interval_minutes') is-invalid @enderror" id="check_interval_minutes" name="check_interval_minutes" type="number" min="1" max="1440" value="{{ old('check_interval_minutes', $account->check_interval_minutes) }}" required>
        <div class="field-help">Dakika cinsinden. Ornek: sicak kaynaklar icin 5, normal kaynaklar icin 15.</div>
        @error('check_interval_minutes') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label class="field-label" for="trust_score">Guven puani <span class="req">*</span></label>
        <input class="input @error('trust_score') is-invalid @enderror" id="trust_score" name="trust_score" type="number" min="0" max="100" value="{{ old('trust_score', $account->trust_score) }}" required>
        @error('trust_score') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field">
        <label class="field-label" for="priority_score">Oncelik puani <span class="req">*</span></label>
        <input class="input @error('priority_score') is-invalid @enderror" id="priority_score" name="priority_score" type="number" min="0" max="100" value="{{ old('priority_score', $account->priority_score) }}" required>
        @error('priority_score') <div class="field-error">{{ $message }}</div> @enderror
    </div>

    <div class="field span-2">
        <label class="switch">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $account->is_active))>
            <span class="track"></span>
            Aktif olarak tara
        </label>
    </div>

    <div class="field span-2">
        <label class="field-label" for="notes">Notlar</label>
        <textarea class="textarea @error('notes') is-invalid @enderror" id="notes" name="notes">{{ old('notes', $account->notes) }}</textarea>
        @error('notes') <div class="field-error">{{ $message }}</div> @enderror
    </div>
</div>

<div class="form-actions">
    <a class="btn btn--secondary" href="{{ route('admin.source-accounts.index') }}">Vazgec</a>
    <div class="spacer"></div>
    <button class="btn btn--primary" type="submit">
        <svg viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z" /><path d="M17 21v-8H7v8" /><path d="M7 3v5h8" /></svg>
        Kaydet
    </button>
</div>
