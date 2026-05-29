@props([
    'eyebrow' => null,
    'title' => null,
    'class' => '',
])

<section {{ $attributes->class(['card', $class]) }}>
    @if ($eyebrow || $title || (isset($actions) && trim((string) $actions) !== ''))
        <div class="card-head">
            <div class="card-title-wrap">
                @if ($eyebrow)
                    <span class="eyebrow">{{ $eyebrow }}</span>
                @endif

                @if ($title)
                    <h2 class="card-title">{{ $title }}</h2>
                @endif
            </div>

            @if (isset($actions) && trim((string) $actions) !== '')
                <div class="card-head-actions">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    {{ $slot }}
</section>
