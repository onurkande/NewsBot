@props([
    'variant' => 'primary',
    'type' => 'button',
    'href' => null,
    'disabled' => false,
])

@php
    $classes = match ($variant) {
        'secondary' => 'btn btn--secondary',
        'danger' => 'btn btn--danger',
        'ghost' => 'btn btn--ghost',
        default => 'btn btn--primary',
    };
@endphp

@if ($href)
    <a {{ $attributes->class([$classes]) }} href="{{ $href }}">
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->class([$classes])->merge(['type' => $type, 'disabled' => $disabled ? true : null]) }}>
        {{ $slot }}
    </button>
@endif
