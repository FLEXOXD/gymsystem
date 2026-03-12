@php
    $mobileNavItems = collect($navItems)->values();
    $mobilePrimaryItems = $mobileNavItems->take(4)->values();
    $mobileOverflowItems = $mobileNavItems->slice(4)->values();
    $mobileHasOverflow = $mobileOverflowItems->isNotEmpty();

    $mobileIsItemActive = static function (array $item): bool {
        $activePatterns = explode('|', (string) ($item['active'] ?? ''));

        return collect($activePatterns)->contains(static fn ($pattern): bool => request()->routeIs(trim((string) $pattern)));
    };

    $mobileOverflowActive = $mobileOverflowItems->contains(static fn ($item): bool => $mobileIsItemActive((array) $item));
@endphp

@if ($mobileHasOverflow)
    <div id="mobile-nav-overflow-backdrop" class="fixed inset-0 z-30 hidden bg-slate-900/55 backdrop-blur-[1px] lg:hidden"></div>
    <section id="mobile-nav-overflow-panel" class="fixed inset-x-2 bottom-[calc(4.4rem+env(safe-area-inset-bottom))] z-40 hidden rounded-2xl border theme-mobile-nav p-2 shadow-2xl lg:hidden">
        <div class="mb-2 px-1">
            <p class="text-[11px] font-black uppercase tracking-[0.14em] theme-nav-link">M&oacute;dulos</p>
        </div>
        <div class="grid grid-cols-2 gap-2">
            @foreach ($mobileOverflowItems as $item)
                @php
                    $isActive = $mobileIsItemActive((array) $item);
                    $isHighlight = (bool) ($item['highlight'] ?? false);
                    $mobileClass = $isActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link';
                    if (! $isActive && $isHighlight) {
                        $mobileClass .= ' theme-nav-mobile-highlight';
                    }
                @endphp
                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                   class="mobile-nav-overflow-link rounded-xl px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide {{ $mobileClass }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </section>
@endif

<nav class="theme-mobile-nav fixed inset-x-0 bottom-0 z-40 border-t p-2 backdrop-blur lg:hidden">
    <div class="mx-auto max-w-full px-1 pb-1">
        <div class="grid gap-2" style="grid-template-columns: repeat({{ $mobileHasOverflow ? 5 : max(1, $mobilePrimaryItems->count()) }}, minmax(0, 1fr));">
            @foreach ($mobilePrimaryItems as $item)
                @php
                    $isActive = $mobileIsItemActive((array) $item);
                    $isHighlight = (bool) ($item['highlight'] ?? false);
                    $mobileClass = $isActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link';
                    if (! $isActive && $isHighlight) {
                        $mobileClass .= ' theme-nav-mobile-highlight';
                    }
                @endphp
                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                   class="rounded-lg px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide {{ $mobileClass }}">
                    {{ $item['label'] }}
                </a>
            @endforeach

            @if ($mobileHasOverflow)
                <button id="mobile-nav-more-button"
                        type="button"
                        aria-expanded="false"
                        aria-controls="mobile-nav-overflow-panel"
                        class="rounded-lg px-2 py-2 text-center text-[11px] font-bold uppercase tracking-wide {{ $mobileOverflowActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link' }}">
                    M&aacute;s
                </button>
            @endif
        </div>
    </div>
</nav>

@if ($mobileHasOverflow)
    <script>
        (function () {
            const moreButton = document.getElementById('mobile-nav-more-button');
            const panel = document.getElementById('mobile-nav-overflow-panel');
            const backdrop = document.getElementById('mobile-nav-overflow-backdrop');
            if (!moreButton || !panel || !backdrop) {
                return;
            }

            const setOpen = (open) => {
                panel.classList.toggle('hidden', !open);
                backdrop.classList.toggle('hidden', !open);
                moreButton.setAttribute('aria-expanded', open ? 'true' : 'false');
            };

            const isOpen = () => !panel.classList.contains('hidden');
            const toggle = () => setOpen(!isOpen());

            moreButton.addEventListener('click', toggle);
            backdrop.addEventListener('click', () => setOpen(false));

            panel.querySelectorAll('.mobile-nav-overflow-link').forEach((link) => {
                link.addEventListener('click', () => setOpen(false));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    setOpen(false);
                }
            });
        })();
    </script>
@endif
