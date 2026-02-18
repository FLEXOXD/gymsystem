@props([
    'variant' => 'default',
])

@php
    $palette = match ($variant) {
        'success' => 'ui-badge-success',
        'danger' => 'ui-badge-danger',
        'warning' => 'ui-badge-warning',
        'info' => 'ui-badge-info',
        'muted' => 'ui-badge-muted',
        default => 'ui-badge-muted',
    };
@endphp

<span {{ $attributes->class("ui-badge {$palette}") }}>
    {{ $slot }}
</span>
