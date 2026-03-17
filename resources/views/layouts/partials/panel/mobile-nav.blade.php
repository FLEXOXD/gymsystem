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

    $mobileIsSupportChatInbox = static function (array $item) use ($isSuperAdmin): bool {
        return (bool) $isSuperAdmin && (string) ($item['icon'] ?? '') === 'inbox';
    };
    $mobileSupportChatUnread = (int) ($supportChatUnread ?? 0);

    $mobileOverflowActive = $mobileOverflowItems->contains(static fn ($item): bool => $mobileIsItemActive((array) $item));
@endphp

<style>
    @media (max-width: 1023px) {
        html.mnav-body-lock {
            overflow: hidden;
        }

        .mnav-shell {
            position: fixed;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 60;
            border-top: 1px solid color-mix(in srgb, var(--border) 84%, transparent);
            background:
                linear-gradient(180deg, color-mix(in srgb, var(--card) 92%, transparent), color-mix(in srgb, var(--card) 96%, #020617));
            backdrop-filter: blur(14px);
            padding: 0.45rem 0.5rem calc(0.45rem + env(safe-area-inset-bottom));
        }

        .mnav-grid {
            display: grid;
            gap: 0.45rem;
            align-items: stretch;
        }

        .mnav-btn {
            min-height: 2.8rem;
            border-radius: 0.8rem;
            border: 1px solid color-mix(in srgb, var(--border) 84%, transparent);
            background: color-mix(in srgb, var(--card) 90%, #020617);
            color: color-mix(in srgb, var(--text) 90%, #ffffff);
            font-size: 0.67rem;
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: 0.01em;
            text-transform: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding-inline: 0.28rem;
            position: relative;
            overflow: hidden;
            transition: border-color 140ms ease, transform 140ms ease, background-color 140ms ease, box-shadow 140ms ease;
        }
        .mnav-chat-badge {
            position: absolute;
            top: 0.16rem;
            right: 0.16rem;
            min-width: 1rem;
            height: 1rem;
            border-radius: 9999px;
            border: 1px solid rgba(245, 158, 11, 0.66);
            background: rgba(245, 158, 11, 0.94);
            color: #1f2937;
            font-size: 0.58rem;
            font-weight: 900;
            line-height: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 0.22rem;
            box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.18);
        }

        .mnav-btn > span {
            display: block;
            width: 100%;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            text-align: center;
        }

        .mnav-btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: 10%;
            right: 10%;
            height: 1px;
            background: color-mix(in srgb, var(--accent) 42%, transparent);
            opacity: 0.75;
            pointer-events: none;
        }

        .mnav-btn-active {
            border-color: color-mix(in srgb, var(--accent) 48%, var(--border));
            background: linear-gradient(145deg, color-mix(in srgb, var(--primary) 34%, var(--card)), color-mix(in srgb, var(--accent) 24%, var(--card)));
            color: #ffffff;
            box-shadow: 0 10px 24px color-mix(in srgb, var(--accent) 24%, transparent);
        }

        .mnav-btn-highlight {
            border-color: color-mix(in srgb, #22c55e 56%, var(--border));
            background: linear-gradient(145deg, color-mix(in srgb, #22c55e 24%, var(--card)), color-mix(in srgb, var(--card) 88%, #020617));
            color: color-mix(in srgb, var(--text) 92%, #ecfdf5);
        }

        .mnav-shell.mnav-sheet-open .mnav-btn.mnav-btn-active,
        .mnav-shell.mnav-sheet-open .mnav-btn.mnav-btn-highlight {
            border-color: color-mix(in srgb, var(--border) 84%, transparent);
            background: color-mix(in srgb, var(--card) 90%, #020617);
            color: color-mix(in srgb, var(--text) 90%, #ffffff);
            box-shadow: none;
        }

        .mnav-shell.mnav-sheet-open #mnav-more-open.mnav-btn-active {
            border-color: color-mix(in srgb, var(--accent) 48%, var(--border));
            background: linear-gradient(145deg, color-mix(in srgb, var(--primary) 34%, var(--card)), color-mix(in srgb, var(--accent) 24%, var(--card)));
            color: #ffffff;
            box-shadow: 0 10px 24px color-mix(in srgb, var(--accent) 24%, transparent);
        }

        .mnav-sheet-backdrop {
            position: fixed;
            inset: 0;
            z-index: 64;
            background: rgba(2, 6, 23, 0.55);
            backdrop-filter: blur(2px);
        }

        .mnav-sheet {
            position: fixed;
            left: max(0.6rem, env(safe-area-inset-left));
            right: max(0.6rem, env(safe-area-inset-right));
            bottom: calc(5.1rem + env(safe-area-inset-bottom));
            z-index: 65;
            border-radius: 1.05rem;
            border: 1px solid color-mix(in srgb, var(--border) 82%, transparent);
            background: linear-gradient(165deg, color-mix(in srgb, var(--card) 94%, #020617), color-mix(in srgb, var(--card) 90%, #020617));
            box-shadow: 0 26px 52px rgba(2, 6, 23, 0.5);
            padding: 0.62rem;
            max-height: min(60vh, 390px);
            overflow: auto;
        }

        .mnav-sheet-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.48rem;
            padding-inline: 0.1rem;
        }

        .mnav-sheet-title {
            font-size: 0.67rem;
            font-weight: 900;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: color-mix(in srgb, var(--muted) 88%, #ffffff);
        }

        .mnav-sheet-close {
            border-radius: 0.58rem;
            border: 1px solid color-mix(in srgb, var(--border) 84%, transparent);
            background: color-mix(in srgb, var(--card) 88%, #000000);
            padding: 0.28rem 0.55rem;
            font-size: 0.66rem;
            font-weight: 700;
            color: color-mix(in srgb, var(--text) 86%, #ffffff);
        }

        .mnav-sheet-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.45rem;
        }

        .mnav-sheet-link {
            min-height: 2.6rem;
            border-radius: 0.76rem;
            border: 1px solid color-mix(in srgb, var(--border) 84%, transparent);
            background: color-mix(in srgb, var(--card) 90%, #020617);
            color: color-mix(in srgb, var(--text) 90%, #ffffff);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 0.72rem;
            font-weight: 700;
            line-height: 1.08;
            padding-inline: 0.5rem;
        }

        .mnav-sheet-link > span {
            display: block;
            width: 100%;
            max-width: 100%;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mnav-sheet-link.mnav-btn-active {
            border-color: color-mix(in srgb, var(--accent) 48%, var(--border));
            background: linear-gradient(145deg, color-mix(in srgb, var(--primary) 30%, var(--card)), color-mix(in srgb, var(--accent) 20%, var(--card)));
            color: #ffffff;
        }

        .mnav-sheet-link.mnav-btn-highlight {
            border-color: color-mix(in srgb, #22c55e 56%, var(--border));
            background: linear-gradient(145deg, color-mix(in srgb, #22c55e 24%, var(--card)), color-mix(in srgb, var(--card) 88%, #020617));
            color: color-mix(in srgb, var(--text) 92%, #ecfdf5);
        }
    }

    @media (max-width: 380px) {
        .mnav-grid {
            gap: 0.36rem;
        }

        .mnav-btn {
            min-height: 2.62rem;
            font-size: 0.61rem;
            padding-inline: 0.18rem;
        }

        .mnav-sheet-link {
            font-size: 0.68rem;
        }
    }
</style>

@if ($mobileHasOverflow)
    <div id="mnav-sheet-backdrop" class="mnav-sheet-backdrop hidden lg:hidden"></div>
    <section id="mnav-sheet" class="mnav-sheet hidden lg:hidden" role="dialog" aria-modal="true" aria-label="Módulos">
        <header class="mnav-sheet-head">
            <p class="mnav-sheet-title">Módulos</p>
            <button type="button" id="mnav-sheet-close" class="mnav-sheet-close">Cerrar</button>
        </header>
        <div class="mnav-sheet-grid">
            @foreach ($mobileOverflowItems as $item)
                @php
                    $isActive = $mobileIsItemActive((array) $item);
                    $isHighlight = (bool) ($item['highlight'] ?? false);
                    $linkClass = $isActive ? 'mnav-btn-active' : '';
                    if (! $isActive && $isHighlight) {
                        $linkClass .= ' mnav-btn-highlight';
                    }
                @endphp
                <a href="{{ route($item['route'], $item['params'] ?? []) }}"
                   class="mnav-sheet-link {{ trim($linkClass) }}">
                    <span>{{ $item['label'] }}</span>
                    @if ($mobileIsSupportChatInbox((array) $item))
                        <span data-support-chat-badge
                              data-count="{{ $mobileSupportChatUnread }}"
                              class="mnav-chat-badge {{ $mobileSupportChatUnread > 0 ? '' : 'hidden' }}">
                            {{ min(99, $mobileSupportChatUnread) }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </section>
@endif

<nav id="mnav-root" class="mnav-shell lg:hidden">
    <div class="mnav-grid" style="grid-template-columns: repeat({{ $mobileHasOverflow ? 5 : max(1, $mobilePrimaryItems->count()) }}, minmax(0, 1fr));">
        @foreach ($mobilePrimaryItems as $item)
            @php
                $isActive = $mobileIsItemActive((array) $item);
                $isHighlight = (bool) ($item['highlight'] ?? false);
                $btnClass = $isActive ? 'mnav-btn-active' : '';
                if (! $isActive && $isHighlight) {
                    $btnClass .= ' mnav-btn-highlight';
                }
            @endphp
            <a href="{{ route($item['route'], $item['params'] ?? []) }}" class="mnav-btn {{ trim($btnClass) }}">
                <span>{{ $mobileShortLabel((array) $item) }}</span>
                @if ($mobileIsSupportChatInbox((array) $item))
                    <span data-support-chat-badge
                          data-count="{{ $mobileSupportChatUnread }}"
                          class="mnav-chat-badge {{ $mobileSupportChatUnread > 0 ? '' : 'hidden' }}">
                        {{ min(99, $mobileSupportChatUnread) }}
                    </span>
                @endif
            </a>
        @endforeach

        @if ($mobileHasOverflow)
            <button id="mnav-more-open"
                    type="button"
                    aria-expanded="false"
                    aria-controls="mnav-sheet"
                    class="mnav-btn {{ $mobileOverflowActive ? 'mnav-btn-active' : '' }}">
                <span>Más</span>
            </button>
        @endif
    </div>
</nav>

@if ($mobileHasOverflow)
    <script>
        (function () {
            if (window.__mnavSheetInit) return;
            window.__mnavSheetInit = true;

            const openButton = document.getElementById('mnav-more-open');
            const closeButton = document.getElementById('mnav-sheet-close');
            const sheet = document.getElementById('mnav-sheet');
            const backdrop = document.getElementById('mnav-sheet-backdrop');
            const navRoot = document.getElementById('mnav-root');
            if (!openButton || !sheet || !backdrop) return;
            const openButtonBaseActive = openButton.classList.contains('mnav-btn-active');

            const setOpen = (open) => {
                sheet.classList.toggle('hidden', !open);
                backdrop.classList.toggle('hidden', !open);
                openButton.setAttribute('aria-expanded', open ? 'true' : 'false');
                openButton.classList.toggle('mnav-btn-active', open || openButtonBaseActive);
                navRoot?.classList.toggle('mnav-sheet-open', open);
                document.documentElement.classList.toggle('mnav-body-lock', open);
            };

            openButton.addEventListener('click', () => setOpen(sheet.classList.contains('hidden')));
            closeButton?.addEventListener('click', () => setOpen(false));
            backdrop.addEventListener('click', () => setOpen(false));

            sheet.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => setOpen(false));
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') setOpen(false);
            });

            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) setOpen(false);
            });
        })();
    </script>
@endif
