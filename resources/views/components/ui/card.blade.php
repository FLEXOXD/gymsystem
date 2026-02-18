@props([
    'title' => null,
    'subtitle' => null,
])

<section {{ $attributes->class('ui-card') }}>
    @if ($title || $subtitle)
        <header class="mb-4">
            @if ($title)
                <h2 class="ui-heading text-lg md:text-xl">{{ $title }}</h2>
            @endif
            @if ($subtitle)
                <p class="ui-muted mt-1 text-sm">{{ $subtitle }}</p>
            @endif
        </header>
    @endif

    {{ $slot }}
</section>
