@extends('admin.layouts.master')

@section('title', 'Twscrape Komutları')
@section('active', 'twscrape-commands')
@section('crumbs', 'Twscrape Yönetimi | Komutlar')

@section('content')
    <x-admin.page-header
        eyebrow="Twscrape Yönetimi"
        title="Komutlar"
        subtitle="Twscrape CLI komutlarını panel üzerinden çalıştırın."
    />

    <x-admin.card eyebrow="CLI" title="Komut Çalıştır">
        <form id="commandForm" style="display:flex; gap:16px; align-items:flex-end; flex-wrap:wrap;">
            <div style="flex:1; min-width:300px;">
                <label class="label">Komut Seç</label>
                <select id="commandSelect" class="select">
                    <option value="accounts">accounts (Tüm hesapları listele)</option>
                    <option value="stats">stats (Hesap istatistiklerini göster)</option>
                    <option value="login_accounts">login_accounts (Tüm hesaplara login ol)</option>
                    <option value="relogin">relogin (Sadece login olmayanlara login ol)</option>
                    <option value="relogin_failed">relogin_failed (Hata verenlere login ol)</option>
                    <option value="reset_locks">reset_locks (Kilitleri sıfırla)</option>
                    <option value="delete_inactive">delete_inactive (Pasifleri sil)</option>
                </select>
            </div>
            <div style="flex:1; min-width:200px;">
                <label class="label">Ek Parametreler (Opsiyonel)</label>
                <input type="text" id="commandParams" class="input" placeholder="Örn: username_to_login">
            </div>
            <button type="submit" class="btn btn--primary" id="runBtn">Çalıştır</button>
        </form>

        <div style="margin-top: 24px;">
            <div style="background:#1e1e1e; color:#0f0; padding:16px; border-radius:8px; font-family:monospace; min-height:300px; max-height:500px; overflow-y:auto; white-space:pre-wrap;" id="consoleOutput">Terminal çıktısı buraya gelecek...</div>
        </div>
    </x-admin.card>

    <script>
        document.getElementById('commandForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const cmd = document.getElementById('commandSelect').value;
            const params = document.getElementById('commandParams').value;
            const fullCmd = cmd + (params ? ' ' + params : '');
            
            const out = document.getElementById('consoleOutput');
            const btn = document.getElementById('runBtn');
            
            out.innerHTML = `> python -m twscrape ${fullCmd}\nÇalıştırılıyor, lütfen bekleyin...\n`;
            btn.disabled = true;
            btn.innerHTML = 'Çalışıyor...';

            fetch('{{ route('admin.twscrape.commands.execute') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ command: fullCmd })
            })
            .then(r => {
                if (!r.ok) {
                    return r.text().then(txt => { throw new Error('HTTP ' + r.status + ': ' + txt.substring(0, 300)); });
                }
                return r.json();
            })
            .then(data => {
                out.innerHTML += `\n[${data.success ? 'BAŞARILI' : 'HATA'}]\n\n${data.output}`;
            })
            .catch(err => {
                out.innerHTML += `\n[SİSTEM HATASI]\n\n${err.message}`;
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = 'Çalıştır';
            });
        });

        // Prefill from query string
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('cmd')) {
            document.getElementById('commandSelect').value = urlParams.get('cmd');
        }
        if (urlParams.has('acc')) {
            document.getElementById('commandParams').value = urlParams.get('acc');
        }
    </script>
@endsection
