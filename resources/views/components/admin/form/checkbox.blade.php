@props([
    'name',
    'label',
    'checked' => false,
])

<label {{ $attributes->class(['check']) }}>
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)>
    <span class="box"></span>
    <span>{{ $label }}</span>
</label>
