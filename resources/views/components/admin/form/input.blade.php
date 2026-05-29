@props([
    'name',
    'label',
    'value' => null,
    'type' => 'text',
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

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        {{ $attributes->except(['class'])->class(['input', 'is-invalid' => $errors->has($name)]) }}
    >

    @error($name)
        <x-admin.validation-error :message="$message" />
    @enderror
</div>
