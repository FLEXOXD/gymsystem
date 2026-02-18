@props([
    'id',
    'title' => null,
    'show' => false,
])

<div id="{{ $id }}"
     {{ $attributes->class('ui-modal-backdrop '.($show ? 'flex' : 'hidden')) }}>
    <div class="ui-modal-panel">
        @if ($title)
            <h3 class="ui-heading text-lg">{{ $title }}</h3>
        @endif
        <div class="{{ $title ? 'mt-2' : '' }} ui-text">
            {{ $slot }}
        </div>
    </div>
</div>
