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
            <div class="alert alert-{{ $message['type'] }}" data-auto-dismiss="4500">
                <div class="alert-body">
                    @if (! empty($message['is_list']))
                        @foreach ($message['text'] as $line)
                            <div>{{ $line }}</div>
                        @endforeach
                    @else
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
