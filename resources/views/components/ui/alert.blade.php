@props([
    'type' => 'info',
    'title' => null,
])

@php
    $palette = match ($type) {
        'success' => 'ui-alert-success',
        'danger' => 'ui-alert-danger',
        'warning' => 'ui-alert-warning',
        default => 'ui-alert-info',
    };
@endphp

<div {{ $attributes->class("ui-alert {$palette}") }}>
    @if ($title)
        <p class="mb-1 text-xs font-bold uppercase tracking-wider">{{ $title }}</p>
    @endif
    <div>{{ $slot }}</div>
</div>
