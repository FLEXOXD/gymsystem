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
                overflow: visible;
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

            .mobile-nav-action > span,
            .mobile-nav-more-link > span {
                display: block;
                width: 100%;
                max-width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .mobile-nav-more {
                position: relative;
            }

            .mobile-nav-more-toggle {
                list-style: none;
                cursor: pointer;
            }

            .mobile-nav-more-toggle::-webkit-details-marker {
                display: none;
            }

            .mobile-nav-more[open] .mobile-nav-more-toggle {
                border: 1px solid color-mix(in srgb, var(--accent) 42%, var(--border));
                background-color: color-mix(in srgb, var(--primary) 26%, var(--card));
                color: var(--text);
            }

            .mobile-nav-more-sheet {
                position: fixed;
                left: max(0.55rem, env(safe-area-inset-left));
                right: max(0.55rem, env(safe-area-inset-right));
                bottom: calc(4.95rem + env(safe-area-inset-bottom));
                z-index: 52;
                display: none;
                max-height: min(58vh, 380px);
                overflow: auto;
                border-radius: 1rem;
                border: 1px solid color-mix(in srgb, var(--border) 86%, transparent);
                background: color-mix(in srgb, var(--card) 94%, #020617);
                box-shadow: 0 26px 52px rgba(2, 6, 23, 0.5);
                padding: 0.6rem;
            }

            .mobile-nav-more[open] .mobile-nav-more-sheet {
                display: block;
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
                font-weight: 700;
                line-height: 1.1;
                padding-inline: 0.55rem;
            }

            .mobile-nav-more-hint {
                font-size: 0.67rem;
                color: color-mix(in srgb, var(--muted) 86%, #ffffff);
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
                <details class="mobile-nav-more">
                    <summary class="mobile-nav-action mobile-nav-more-toggle {{ $mobileOverflowActive ? 'theme-nav-mobile-active' : 'theme-nav-mobile-link' }}">
                        <span>M&aacute;s</span>
                    </summary>
                    <section class="mobile-nav-more-sheet" role="dialog" aria-label="Módulos">
                        <div class="mobile-nav-more-head">
                            <p class="text-[11px] font-black uppercase tracking-[0.14em] theme-nav-link">M&oacute;dulos</p>
                            <span class="mobile-nav-more-hint">Pulsa M&aacute;s para cerrar</span>
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
                                   class="mobile-nav-more-link {{ $mobileClass }}">
                                    <span>{{ $item['label'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </section>
                </details>
            @endif
        </div>
    </div>
</nav>
