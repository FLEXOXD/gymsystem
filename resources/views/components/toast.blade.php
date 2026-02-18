@props([
    'type' => 'info',
    'autohide' => true,
    'delay' => 4200,
])

@php
    $palette = match ($type) {
        'success' => 'ui-alert-success',
        'danger' => 'ui-alert-danger',
        'warning' => 'ui-alert-warning',
        default => 'ui-alert-info',
    };
@endphp

<div data-toast data-autohide="{{ $autohide ? '1' : '0' }}" data-delay="{{ $delay }}"
     {{ $attributes->class("ui-alert {$palette}") }}>
    {{ $slot }}
</div>
