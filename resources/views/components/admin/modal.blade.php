@props([
    'id',
    'title' => null,
    'message' => null,
])

<div id="{{ $id }}" class="modal-overlay" data-modal data-confirm-delete-modal hidden aria-hidden="true">
    <div class="modal-card" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
        <div class="modal-head">
            <div>
                @if ($title)
                    <h3 class="modal-title" id="{{ $id }}-title" data-confirm-delete-title>{{ $title }}</h3>
                @endif

                @if ($message)
                    <p class="modal-message" data-modal-message data-confirm-delete-message>{{ $message }}</p>
                @endif
            </div>

            <button type="button" class="btn--icon" data-modal-close aria-label="Kapat">
                <svg viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></svg>
            </button>
        </div>

        <div class="modal-body">
            {{ $slot }}
        </div>
    </div>
</div>
