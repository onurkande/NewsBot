@props([
    'eyebrow' => null,
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->class(['hero']) }}>
    <div class="hero-text">
        @if ($eyebrow)
            <span class="eyebrow">{{ $eyebrow }}</span>
        @endif

        @if ($title)
            <h1 class="hero-title">{{ $title }}</h1>
        @endif

        @if ($subtitle)
            <p class="hero-sub">{{ $subtitle }}</p>
        @endif
    </div>

    @if (isset($actions) && trim((string) $actions) !== '')
        <div class="hero-actions">
            {{ $actions }}
        </div>
    @endif
</section>
