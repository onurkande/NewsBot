@props([
    'name',
    'label',
    'value' => null,
    'placeholder' => null,
    'required' => false,
])

<div {{ $attributes->class(['field']) }}>
    <label class="field-label" for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="req">*</span>
        @endif
    </label>

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        {{ $attributes->except(['class'])->class(['textarea', 'is-invalid' => $errors->has($name)]) }}
    >{{ $value }}</textarea>

    @error($name)
        <x-admin.validation-error :message="$message" />
    @enderror
</div>
