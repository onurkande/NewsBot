@php
    $isEdit = isset($account);
@endphp

<div class="form-row">
    <x-admin.form.input
        name="username"
        label="Kullanici Adi (@kullaniciadi)"
        :value="old('username', $isEdit ? $account->username : '')"
        required
    />
    <x-admin.form.input
        name="display_name"
        label="Gorunen Ad"
        :value="old('display_name', $isEdit ? $account->display_name : '')"
    />
</div>

<div class="form-row">
    <x-admin.form.input
        name="auth_token"
        label="Auth Token"
        type="password"
        :value="old('auth_token', $isEdit ? $account->auth_token : '')"
        required
    />
    <x-admin.form.input
        name="ct0"
        label="CT0"
        type="password"
        :value="old('ct0', $isEdit ? $account->ct0 : '')"
        required
    />
</div>

<div class="form-row">
    <x-admin.form.textarea
        name="cookies_json"
        label="Cookies JSON (opsiyonel)"
        :value="old('cookies_json', $isEdit ? json_encode($account->cookies_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) : '')"
        rows="4"
    />
    <x-admin.form.checkbox
        name="is_active"
        label="Aktif"
        :checked="old('is_active', $isEdit ? $account->is_active : true)"
    />
</div>

<div class="form-row">
    <x-admin.form.input
        name="account_created_at"
        label="Hesap Olusturma Tarihi (warmup modu icin)"
        type="datetime-local"
        :value="old('account_created_at', $isEdit && $account->account_created_at ? $account->account_created_at->format('Y-m-d\TH:i') : '')"
    />
</div>
