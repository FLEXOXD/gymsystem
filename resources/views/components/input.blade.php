@props([
    'label' => null,
    'name' => null,
])

@if ($label)
    <label class="space-y-1 text-sm font-semibold ui-muted">
        <span>{{ $label }}</span>
        <input name="{{ $name }}" {{ $attributes->class('ui-input') }}>
    </label>
@else
    <input name="{{ $name }}" {{ $attributes->class('ui-input') }}>
@endif
