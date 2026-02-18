@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $variants = [
        'primary' => 'ui-button-primary',
        'secondary' => 'ui-button-secondary',
        'success' => 'ui-button-success',
        'danger' => 'ui-button-danger',
        'muted' => 'ui-button-muted',
        'ghost' => 'ui-button-ghost',
    ];
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs font-bold',
        'md' => 'px-4 py-2 text-sm font-semibold',
        'lg' => 'px-5 py-3 text-base font-bold',
    ];
    $buttonClass = 'ui-button '
        .($variants[$variant] ?? $variants['primary']).' '
        .($sizes[$size] ?? $sizes['md']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($buttonClass) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($buttonClass) }}>
        {{ $slot }}
    </button>
@endif

