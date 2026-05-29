@props([
    'name',
    'label',
    'options' => [],
    'selected' => null,
    'required' => false,
])

<div {{ $attributes->class(['field']) }}>
    <label class="field-label" for="{{ $name }}">
        {{ $label }}
        @if ($required)
            <span class="req">*</span>
        @endif
    </label>

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @required($required)
        {{ $attributes->except(['class'])->class(['select', 'is-invalid' => $errors->has($name)]) }}
    >
        @foreach ($options as $value => $labelText)
            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $labelText }}</option>
        @endforeach
    </select>

    @error($name)
        <x-admin.validation-error :message="$message" />
    @enderror
</div>
