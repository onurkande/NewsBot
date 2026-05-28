@if (session('success'))
    <div class="alert success" style="margin-bottom: 16px">
        <div class="ico">
            <svg viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5" /></svg>
        </div>
        <div class="body">
            <div class="title">Islem tamamlandi</div>
            <div>{{ session('success') }}</div>
        </div>
    </div>
@endif

@if ($errors->any())
    <div class="alert danger" style="margin-bottom: 16px">
        <div class="ico">
            <svg viewBox="0 0 24 24"><path d="M12 9v4M12 17h.01" /><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z" /></svg>
        </div>
        <div class="body">
            <div class="title">Formu kontrol edin</div>
            <div>Eksik veya hatali alanlar var.</div>
        </div>
    </div>
@endif
