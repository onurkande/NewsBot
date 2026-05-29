@props(['message' => null])

@if ($message)
    <div {{ $attributes->class(['field-error']) }}>{{ $message }}</div>
@endif
