@php
    $mobileNavItems = collect($navItems)->values();
    $mobilePrimaryItems = $mobileNavItems->take(4)->values();
    $mobileOverflowItems = $mobileNavItems->slice(4)->values();
    $mobileHasOverflow = $mobileOverflowItems->isNotEmpty();
    $mobileShortLabelsByIcon = [
        'panel' => 'Panel',
        'reception' => 'Recepción',
        'clients' => 'Clientes',
        'sales_inventory' => 'Ventas',
        'products' => 'Productos',
        'plans' => 'Planes',
        'cash' => 'Caja',
        'staff' => 'Cajeros',
        'reports' => 'Reportes',
        'branches' => 'Sucursales',
        'client_portal' => 'Portal',
        'notifications' => 'Avisos',
        'suggestions' => 'Ideas',
        'quotations' => 'Cotizaciones',
        'gym_directory' => 'Gimnasios',
        'subscriptions_admin' => 'Suscripciones',
        'gym_create' => 'Nuevo gym',
        'inbox' => 'Mensajes',
        'legal_acceptances' => 'Legales',
        'web' => 'Web',
    ];

    $mobileIsItemActive = static function (array $item): bool {
        $activePatterns = explode('|', (string) ($item['active'] ?? ''));

        return collect($activePatterns)->contains(static fn ($pattern): bool => request()->routeIs(trim((string) $pattern)));
    };
    $mobileShortLabel = static function (array $item) use ($mobileShortLabelsByIcon): string {
        $icon = (string) ($item['icon'] ?? '');
        $label = trim((string) ($item['label'] ?? ''));

        if ($icon !== '' && isset($mobileShortLabelsByIcon[$icon])) {
            return (string) $mobileShortLabelsByIcon[$icon];
        }

        return $label !== '' ? $label : 'Módulo';
    };

    $mobileOverflowActive = $mobileOverflowItems->contains(static fn ($item): bool => $mobileIsItemActive((array) $item));
@endphp

@push('styles')
    <style>
        @media (max-width: 1023px) {
            .mobile-nav-bar {
                padding-top: 0.4rem;
                padding-bottom: calc(0.4rem + env(safe-area-inset-bottom));
            }

            .mobile-nav-grid {
                align-items: stretch;
            }

            .mobile-nav-action {
                min-height: 2.7rem;
                border-radius: 0.72rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                line-height: 1.05;
                font-size: 0.66rem;
                font-weight: 800;
                letter-spacing: 0.015em;
                text-transform: none;
                padding-inline: 0.3rem;
            }

            .mobile-nav-action > span {
                display: block;
                width: 100%;
                max-width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mobile-nav-more-sheet {
                inset-inline: max(0.55rem, env(safe-area-inset-left));
                bottom: calc(4.95rem + env(safe-area-inset-bottom));
                max-height: min(58vh, 380px);
                overflow: auto;
                border-radius: 1rem;
                box-shadow: 0 26px 52px rgba(2, 6, 23, 0.5);
            }

            .mobile-nav-more-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 0.5rem;
                padding-inline: 0.15rem;
            }

            .mobile-nav-more-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.45rem;
            }

            .mobile-nav-more-link {
                min-height: 2.6rem;
                border-radius: 0.75rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                text-transform: none;
                font-size: 0.72rem;
                line-height: 1.1;
                padding-inline: 0.55rem;
            }
        }

        @media (max-width: 380px) {
            .mobile-nav-grid {
                gap: 0.38rem;
            }

            .mobile-nav-action {
                font-size: 0.61rem;
                padding-inline: 0.2rem;
            }
        }
    </style>
@endpush

@if ($mobileHasOverflow)
    <div id="mobile-nav-overflow-backdrop" class="fixed inset-0 z-[44] hidden bg-slate-900/40 backdrop-blur-[1px] lg:hidden"></div>
    <section id="mobile-nav-overflow-panel" class="mobile-nav-more-sheet fixed z-[50] hidden border theme-mobile-nav p-2 lg:hidden" role="dialog" aria-modal="true" aria-label="Módulos">
        <div class="mobile-nav-more-head">
            <p class="text-[11px] font-black uppercase tracking-[0.14em] theme-nav-link">M&oacute;dulos</p>
            <button id="mobile-nav-overflow-close" type="button" class="ui-button ui-button-ghost px-2 py-1 text-[11px] font-bold">Cerrar</button>
        </div>
        <div class="mobile-nav-more-grid mt-2">
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
                   class="mobile-nav-overflow-link mobile-nav-more-link text-center font-semibold {{ $mobileClass }}">
                    {{ $item['label'] }}
                </a>
            @endforeach
        </div>
    </section>
@endif

<nav class="theme-mobile-nav mobile-nav-bar fixed inset-x-0 bottom-0 z-40 border-t backdrop-blur lg:hidden">
    <div class="mx-auto max-w-full px-1 pb-1">
        <div class="mobile-nav-grid grid gap-2" style="grid-template-columns: repeat({{ $mobileHasOverflow ? 5 : max(1, $mobilePrimaryItems->count()) }}, minmax(0, 1fr));">
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
                   class="mobile-nav-action {{ $mobileClass }}">
                    <span>{{ $mobileShortLabel((array) $item) }}</span>
                </a>
            @endforeach

            @if ($mobileHasOverflow)
                <button id="mobile-nav-more-button"
                        type="button"
                        aria-expanded="false"
                        aria-controls="mobile-nav-overflow-panel"
                        class="mobile-nav-action {{ $mobileOverflowActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link' }}">
                    <span>M&aacute;s</span>
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
            const closeButton = document.getElementById('mobile-nav-overflow-close');
            if (!moreButton || !panel || !backdrop) {
                return;
            }

            const setOpen = (open) => {
                panel.classList.toggle('hidden', !open);
                backdrop.classList.toggle('hidden', !open);
                moreButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                document.documentElement.classList.toggle('overflow-hidden', open);
            };

            const isOpen = () => !panel.classList.contains('hidden');
            const toggle = () => setOpen(!isOpen());

            moreButton.addEventListener('click', toggle);
            backdrop.addEventListener('click', () => setOpen(false));
            closeButton?.addEventListener('click', () => setOpen(false));

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
