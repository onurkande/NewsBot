@props([
    'title' => 'Kayıt yok',
    'description' => null,
])

<div {{ $attributes->class(['empty-state']) }}>
    <div class="empty-state-title">{{ $title }}</div>
    @if ($description)
        <div class="empty-state-desc">{{ $description }}</div>
    @endif
    {{ $slot }}
</div>
