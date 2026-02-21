@props([
    'title' => 'Sin resultados',
    'message' => 'No hay información para mostrar.',
])

<div {{ $attributes->class('ui-card border-dashed text-center') }}>
    <p class="ui-heading text-base">{{ $title }}</p>
    <p class="ui-muted mt-1 text-sm">{{ $message }}</p>
    {{ $slot }}
</div>
