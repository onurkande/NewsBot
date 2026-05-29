@php
    $messages = [];

    if (session('success')) {
        $messages[] = ['type' => 'success', 'text' => session('success')];
    }

    if (session('error')) {
        $messages[] = ['type' => 'danger', 'text' => session('error')];
    }

    if ($errors->any()) {
        $messages[] = [
            'type' => 'danger',
            'text' => $errors->all(),
            'is_list' => true,
        ];
    }
@endphp

@if ($messages !== [])
    <div class="stack" style="margin-bottom: 16px;">
        @foreach ($messages as $message)
            <div class="alert {{ $message['type'] }}" data-auto-dismiss="4500">
                <div class="ico">
                    @if ($message['type'] === 'success')
                        <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
                    @else
                        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    @endif
                </div>

                <div class="body">
                    @if (! empty($message['is_list']))
                        <div class="title">Lütfen hataları düzeltin</div>
                        @foreach ($message['text'] as $line)
                            <div>{{ $line }}</div>
                        @endforeach
                    @else
                        <div class="title">{{ $message['type'] === 'success' ? 'Başarılı' : 'Hata' }}</div>
                        {{ $message['text'] }}
                    @endif
                </div>

                <button type="button" class="close" aria-label="Dismiss">
                    <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg>
                </button>
            </div>
        @endforeach
    </div>
@endif
