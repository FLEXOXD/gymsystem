@php
    $premiumDemoLabel = 'Probar la demo gratis';
    $premiumQuoteLabel = 'Solicita tu cotización';
    $premiumHeroKicker = trim((string) ($content['hero_kicker'] ?? 'Software para dueños de gimnasios'));
    $premiumHeroTitle = trim((string) ($content['hero_title'] ?? ''));
    $premiumHeroTitle = $premiumHeroTitle !== ''
        ? $premiumHeroTitle
        : "Deja de perder\nclientes y haz un\ngimnasio\ninteligente";
    $premiumHeroTitleNormalized = mb_strtolower(preg_replace('/\s+/u', ' ', trim(strip_tags((string) $premiumHeroTitle))));
    if ($premiumHeroTitleNormalized === 'deja de perder clientes y haz un gimnasio inteligente') {
        $premiumHeroTitleLines = [
            'Deja de perder',
            'clientes y haz un',
            'gimnasio',
            'inteligente',
        ];
    } else {
        $premiumHeroTitleLines = preg_split('/\R+/', str_ireplace(['<br />', '<br/>', '<br>'], "\n", $premiumHeroTitle)) ?: [];
        $premiumHeroTitleLines = array_values(array_filter(array_map(static fn ($line) => trim(strip_tags((string) $line)), $premiumHeroTitleLines), static fn ($line) => $line !== ''));
        if ($premiumHeroTitleLines === []) {
            $premiumHeroTitleLines = [trim(strip_tags($premiumHeroTitle))];
        }
    }
    $premiumHeroSubtitle = trim((string) ($content['hero_subtitle'] ?? ''));
    $premiumHeroSubtitle = $premiumHeroSubtitle !== ''
        ? $premiumHeroSubtitle
        : 'Administra clientes, membresías, caja, accesos y reportes desde una sola plataforma. Cobra a tiempo, controla renovaciones y lleva tu operación diaria con más orden desde escritorio o móvil.';

    $premiumHeroPoints = [
        [
            'title' => 'Cobra a tiempo',
            'text' => 'Controla renovaciones, pagos pendientes y caja diaria sin depender de procesos manuales.',
        ],
        [
            'title' => 'Opera con más orden',
            'text' => 'Recepción, clientes y accesos trabajan desde una sola plataforma para evitar errores y atrasos.',
        ],
        [
            'title' => 'Crece con control',
            'text' => 'Revisa reportes, movimiento y estado del gimnasio desde celular o escritorio.',
        ],
    ];

    $premiumBenefits = [
        [
            'icon' => 'speed',
            'title' => 'Experiencia ágil para tu equipo',
            'text' => 'Recepción, caja y accesos en un flujo más directo para atender mejor desde el primer clic.',
        ],
        [
            'icon' => 'pulse',
            'title' => 'Módulos modernos',
            'text' => 'Tus módulos clave se presentan como piezas sólidas del sistema: cobros, reportes, control y PWA.',
        ],
        [
            'icon' => 'proof',
            'title' => 'Resultados visibles',
            'text' => 'La propuesta comercial transmite orden, confianza y una percepción de valor mucho más alta.',
        ],
        [
            'icon' => 'clock',
            'title' => 'Flexibilidad real',
            'text' => 'La estructura sigue siendo flexible para la demo, la cotización o el inicio de sesión, sin perder la lógica actual.',
        ],
    ];

    $premiumModuleVisuals = [
        'reception' => asset('marketing/interface-reception.svg?v=20260324'),
        'control' => asset('marketing/interface-control.svg?v=20260324'),
        'mobile' => asset('marketing/interface-mobile.svg?v=20260324'),
        'pricing' => asset('marketing/interface-pricing.svg?v=20260324'),
    ];

    $capabilityModules = [
        [
            'title' => trim((string) ($content['section_1_title'] ?? 'Recepción, caja y accesos')),
            'text' => trim((string) ($content['section_1_text'] ?? 'Cobras, validas accesos y mueves la operación diaria desde una sola interfaz.')),
            'key' => 'reception',
            'image' => $premiumModuleVisuals['reception'],
            'cta' => trim((string) ($content['section_1_cta_label'] ?? 'Ver demo guiada')),
            'href' => trim((string) ($content['section_1_cta_url'] ?? '')) !== '' ? trim((string) ($content['section_1_cta_url'] ?? '')) : route('demo.guide'),
            'modal_button_label' => trim((string) ($content['section_1_modal_button_label'] ?? 'Ver detalle')),
            'modal_badge' => trim((string) ($content['section_1_modal_badge'] ?? 'Recepción en vivo')),
            'modal_title' => trim((string) ($content['section_1_modal_title'] ?? 'Ve cómo recepción, caja y acceso trabajan en una sola interfaz.')),
            'modal_text' => trim((string) ($content['section_1_modal_text'] ?? 'Usa este modal para explicar el flujo real: recepción registra, cobra, valida acceso y sigue al cliente sin saltar entre pantallas innecesarias.')),
            'modal_items' => [
                trim((string) ($content['section_1_modal_item_1'] ?? 'Alta rápida del cliente y validación de acceso en un solo paso.')),
                trim((string) ($content['section_1_modal_item_2'] ?? 'Cobro y renovación visibles para recepción y administración.')),
                trim((string) ($content['section_1_modal_item_3'] ?? 'Lectura clara para equipos que necesitan rapidez y orden diario.')),
            ],
            'modal_image' => $premiumModuleVisuals['reception'],
            'modal_action_mode' => 'demo',
            'modal_action_label' => $premiumDemoLabel,
            'modal_action_href' => '',
            'modal_note' => 'Se abre un gimnasio temporal con datos de ejemplo para que pruebes este flujo sin tocar cuentas reales.',
        ],
        [
            'title' => trim((string) ($content['section_2_title'] ?? 'Control comercial y reportes')),
            'text' => trim((string) ($content['section_2_text'] ?? 'Renovaciones, ingresos y seguimiento aparecen con un diseño más editorial y fácil de entender.')),
            'key' => 'control',
            'image' => $premiumModuleVisuals['control'],
            'cta' => trim((string) ($content['section_2_cta_label'] ?? 'Ver demo operativa')),
            'href' => trim((string) ($content['section_2_cta_url'] ?? '')) !== '' ? trim((string) ($content['section_2_cta_url'] ?? '')) : route('demo'),
            'modal_button_label' => trim((string) ($content['section_2_modal_button_label'] ?? 'Ver detalle')),
            'modal_badge' => trim((string) ($content['section_2_modal_badge'] ?? 'Control y reportes')),
            'modal_title' => trim((string) ($content['section_2_modal_title'] ?? 'Muestra reportes, renovaciones y control con una escena mucho más clara.')),
            'modal_text' => trim((string) ($content['section_2_modal_text'] ?? 'Este modal sirve para aterrizar mejor el valor del sistema: qué ve administración, cómo entiende renovaciones y dónde detecta alertas o movimiento operativo.')),
            'modal_items' => [
                trim((string) ($content['section_2_modal_item_1'] ?? 'Seguimiento de renovaciones y vencimientos sin perder contexto.')),
                trim((string) ($content['section_2_modal_item_2'] ?? 'Reportes e indicadores listos para revisar rápido.')),
                trim((string) ($content['section_2_modal_item_3'] ?? 'Mejor lectura comercial para vender orden y control real.')),
            ],
            'modal_image' => $premiumModuleVisuals['control'],
            'modal_action_mode' => 'demo',
            'modal_action_label' => $premiumDemoLabel,
            'modal_action_href' => '',
            'modal_note' => 'La demo entra con reportes, renovaciones y alertas para que el valor se entienda de inmediato.',
        ],
        [
            'title' => trim((string) ($content['section_3_title'] ?? 'PWA y experiencia móvil')),
            'text' => trim((string) ($content['section_3_text'] ?? 'El producto se siente más moderno, listo para escritorio, tableta y móvil.')),
            'key' => 'mobile',
            'image' => $premiumModuleVisuals['mobile'],
            'cta' => trim((string) ($content['section_3_cta_label'] ?? 'Ver uso en celular')),
            'href' => trim((string) ($content['section_3_cta_url'] ?? '')) !== '' ? trim((string) ($content['section_3_cta_url'] ?? '')) : route('landing').'#faq-movil',
            'modal_button_label' => trim((string) ($content['section_3_modal_button_label'] ?? 'Ver detalle')),
            'modal_badge' => trim((string) ($content['section_3_modal_badge'] ?? 'PWA y móvil')),
            'modal_title' => trim((string) ($content['section_3_modal_title'] ?? 'Enseña mejor la experiencia móvil para operar desde celular o tablet.')),
            'modal_text' => trim((string) ($content['section_3_modal_text'] ?? 'Aquí puedes explicar cómo se instala la PWA, qué tan cómodo se usa en recepción y por qué se siente como una app lista para trabajar.')),
            'modal_items' => [
                trim((string) ($content['section_3_modal_item_1'] ?? 'Instalable en celular y escritorio con entrada directa.')),
                trim((string) ($content['section_3_modal_item_2'] ?? 'Interfaz adaptada para recepción, caja y operación diaria.')),
                trim((string) ($content['section_3_modal_item_3'] ?? 'Base visual perfecta para explicar movilidad y velocidad.')),
            ],
            'modal_image' => $premiumModuleVisuals['mobile'],
            'modal_action_mode' => 'demo',
            'modal_action_label' => $premiumDemoLabel,
            'modal_action_href' => '',
            'modal_note' => 'La misma demo funciona en escritorio y celular, con acceso temporal para revisar recepción y operación diaria.',
        ],
        [
            'title' => 'Planes y escalabilidad',
            'text' => 'La sección de precios gana más presencia para ayudarte a elegir el plan correcto sin ruido.',
            'key' => 'pricing',
            'image' => $premiumModuleVisuals['pricing'],
            'cta' => 'Ver precios y planes',
            'href' => route('landing').'#pricing',
            'modal_button_label' => 'Ver detalle',
            'modal_badge' => 'Planes y escalabilidad',
            'modal_title' => 'Compara planes dentro de la misma experiencia, sin pantallas sueltas.',
            'modal_text' => 'La sección comercial ya no te manda a páginas separadas: ves el enfoque visual y luego bajas directo al comparador.',
            'modal_items' => [
                'Comparas planes para una sede, control operativo y multi-sede.',
                'Tienes más contexto visual antes de hablar de precio.',
                'Todo mantiene un solo lenguaje de marca y una sola experiencia.',
            ],
            'modal_image' => $premiumModuleVisuals['pricing'],
            'modal_action_mode' => 'link',
            'modal_action_label' => 'Ver precios y planes',
            'modal_action_href' => '#pricing',
            'modal_note' => 'Desde aqui bajas directo al comparador comercial de esta misma portada.',
        ],
    ];

    $heroMediaSources = array_values(array_filter([
        $heroSlides[0] ?? null,
        $heroSlides[1] ?? null,
        $heroSlides[2] ?? null,
        $heroSlides[3] ?? null,
        $homePageBackgroundUrls[0] ?? null,
        $homePageBackgroundUrls[1] ?? null,
        $homePageBackgroundUrls[2] ?? null,
        $homePageBackgroundUrls[3] ?? null,
        $homePageBackgroundUrls[4] ?? null,
    ], static fn ($value) => is_string($value) && trim($value) !== ''));

    $premiumHeroImage = $heroMediaSources[0] ?? 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1800&q=80';
    $premiumCommunityImage = $heroMediaSources[1] ?? $premiumHeroImage;
    $premiumPromoFallback = $heroMediaSources[2] ?? $premiumHeroImage;
    $premiumPricingFallback = $heroMediaSources[3] ?? $premiumCommunityImage;

    $premiumProgramCards = collect($capabilityModules)
        ->map(function (array $module, int $index) use ($heroMediaSources, $premiumPromoFallback): array {
            $image = trim((string) ($module['image'] ?? ''));

            if ($image === '') {
                $image = $heroMediaSources[$index] ?? $premiumPromoFallback;
            }

            $href = trim((string) ($module['href'] ?? '#features'));

            return [
                'title' => trim((string) ($module['title'] ?? 'Módulo')),
                'text' => trim((string) ($module['text'] ?? '')),
                'image' => $image,
                'cta' => trim((string) ($module['cta'] ?? 'Ver más')),
                'href' => $href !== '' ? $href : '#features',
                'modal_button_label' => trim((string) ($module['modal_button_label'] ?? '')),
                'modal_badge' => trim((string) ($module['modal_badge'] ?? '')),
                'modal_title' => trim((string) ($module['modal_title'] ?? '')),
                'modal_text' => trim((string) ($module['modal_text'] ?? '')),
                'modal_items' => collect((array) ($module['modal_items'] ?? []))
                    ->filter(fn ($item) => is_string($item) && trim($item) !== '')
                    ->values()
                    ->all(),
                'modal_image' => trim((string) ($module['modal_image'] ?? '')),
                'modal_secondary_image' => trim((string) ($module['modal_secondary_image'] ?? '')),
            ];
        })
        ->values();

    $premiumVisualSlides = collect([
        [
            'eyebrow' => 'Recepción activa',
            'nav' => 'Recepción',
            'title' => 'Cobra y valida accesos sin fricción.',
            'text' => 'Recepción ve clientes, pagos y accesos en una sola pantalla para atender rápido.',
            'image' => $heroMediaSources[0] ?? $premiumHeroImage,
        ],
        [
            'eyebrow' => 'Cobros en marcha',
            'nav' => 'Cobros',
            'title' => 'Membresías y pagos siempre bajo control.',
            'text' => 'Renovaciones, vencimientos y caja quedan visibles para cobrar a tiempo.',
            'image' => $heroMediaSources[1] ?? $premiumCommunityImage,
        ],
        [
            'eyebrow' => 'Lectura operativa',
            'nav' => 'Reportes',
            'title' => 'Reportes y alertas para decidir rápido.',
            'text' => 'Detecta atrasos, movimiento e indicadores clave sin perder tiempo entre pantallas.',
            'image' => $heroMediaSources[2] ?? $premiumPromoFallback,
        ],
        [
            'eyebrow' => 'Experiencia móvil',
            'nav' => 'PWA',
            'title' => 'Controla tu gimnasio desde móvil o escritorio.',
            'text' => 'La PWA te deja seguir la operación diaria desde donde trabajes.',
            'image' => $heroMediaSources[3] ?? $premiumPricingFallback,
        ],
    ])->values();

    $initialPremiumVisualSlide = $premiumVisualSlides->first();

    $premiumRoleCards = [
        [
            'role' => 'Recepción',
            'text' => 'Un frente visualmente fuerte para mostrar ingresos, caja y atención en una sola experiencia.',
            'image' => trim((string) ($aboutTeamImage1 ?? '')) !== '' ? trim((string) $aboutTeamImage1) : $premiumHeroImage,
        ],
        [
            'role' => 'Administración',
            'text' => 'Reportes, renovaciones y seguimiento con una presentación más sólida y fácil de leer.',
            'image' => trim((string) ($aboutTeamImage2 ?? '')) !== '' ? trim((string) $aboutTeamImage2) : $premiumCommunityImage,
        ],
        [
            'role' => 'Comercial',
            'text' => 'La portada impulsa mejor la cotización y hace que el producto se sienta más vendible.',
            'image' => trim((string) ($aboutTeamImage3 ?? '')) !== '' ? trim((string) $aboutTeamImage3) : $premiumPromoFallback,
        ],
        [
            'role' => 'Dirección',
            'text' => 'Precios y cierre con una narrativa más decidida para gimnasios que quieren crecer con orden.',
            'image' => trim((string) ($aboutStoryImage ?? '')) !== '' ? trim((string) $aboutStoryImage) : $premiumPricingFallback,
        ],
    ];

    $premiumReviewCards = [
        [
            'eyebrow' => 'Recepción',
            'text' => 'La página principal ya no solo informa: ahora transmite velocidad, orden y una sensación de marca más fuerte.',
        ],
        [
            'eyebrow' => 'Operación',
            'text' => 'Los módulos se entienden más rápido y el usuario identifica en segundos dónde está el valor del sistema.',
        ],
        [
            'eyebrow' => 'Conversión',
            'text' => 'La cotización gana protagonismo sin romper la demo ni el inicio de sesión, que siguen funcionando igual.',
        ],
    ];

    $premiumStatHighlights = [
        [
            'value' => number_format((int) ($stats['gyms'] ?? 0)),
            'label' => 'SEDES',
        ],
        [
            'value' => number_format((int) ($stats['active_sessions'] ?? 0)),
            'label' => 'ACTIVOS',
        ],
        [
            'value' => 'WEB',
            'label' => 'PANEL',
        ],
        [
            'value' => 'PWA',
            'label' => 'MÓVIL',
        ],
    ];

    $planSegments = [
        'basico' => 'Base',
        'profesional' => 'Control',
        'premium' => 'Avanzado',
        'sucursales' => 'Multi-sede',
    ];
@endphp

<section id="inicio" class="shell premium-hero-section page-intro-item" style="--page-intro-delay: 160ms; --page-intro-distance: 26px;">
    <div class="premium-hero-layout">
        <div class="premium-hero-copy reveal" style="--reveal-delay: 180ms; --reveal-distance: 28px; --reveal-duration: .95s;">
            <div class="premium-hero-topline">
                <span class="premium-kicker">{{ $premiumHeroKicker }}</span>
                <span class="premium-chip">Ecuador y Latinoamérica</span>
            </div>

            <h1 class="premium-hero-title">
                @foreach ($premiumHeroTitleLines as $line)
                    <span>{{ $line }}</span>
                @endforeach
            </h1>
            <p class="premium-hero-text">{{ $premiumHeroSubtitle }}</p>

            <div class="premium-hero-actions" data-reveal-group data-reveal-delay="280" data-reveal-step="95">
                <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                    @csrf
                    <button class="btn btn-demo" type="submit">{{ $premiumDemoLabel }}</button>
                </form>

                <button class="btn btn-wa btn-quote-trigger"
                        type="button"
                        data-open-quote-modal
                        data-quote-source="hero_primary"
                        aria-controls="quote-request-modal">
                    <span class="quote-cta-icon-badge" aria-hidden="true">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                            <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>{{ $premiumQuoteLabel }}</span>
                </button>

                <a class="btn btn-ghost" href="#pricing">Ver planes</a>
            </div>

            <p class="premium-hero-note">
                La demo sigue creando un entorno temporal aislado para este dispositivo. Si ya eres cliente,
                <a href="{{ route('login') }}">{{ $loginLabel }}</a>.
            </p>

            <div class="premium-hero-points" data-reveal-group data-reveal-delay="420" data-reveal-step="90">
                @foreach ($premiumHeroPoints as $point)
                    <article class="premium-hero-point">
                        <span class="premium-point-bullet" aria-hidden="true"></span>
                        <div>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="premium-hero-visual reveal" style="--reveal-delay: 320ms; --reveal-distance: 34px; --reveal-duration: 1.05s;">
            <div class="premium-screen-shell">
                <div class="premium-screen-frame" data-premium-visual-carousel>
                    <div class="premium-screen-toolbar">
                        <span class="premium-screen-brand">{{ $brandName }}</span>
                        <span class="premium-screen-chip">CONTROL TOTAL</span>
                    </div>

                    <div class="premium-screen-canvas">
                        <div class="premium-screen-overlay"></div>

                        <div class="premium-screen-main">
                            <div class="premium-screen-copy">
                                <span data-premium-visual-eyebrow>{{ $initialPremiumVisualSlide['eyebrow'] ?? 'Control total' }}</span>
                                <strong data-premium-visual-title>{{ $initialPremiumVisualSlide['title'] ?? 'Lleva tu gimnasio a otro nivel con una portada más potente.' }}</strong>
                                <p data-premium-visual-text>{{ $initialPremiumVisualSlide['text'] ?? 'Cobros, accesos, operación y planes dentro de una narrativa visual más intensa.' }}</p>
                            </div>

                            <div class="premium-screen-media">
                                @foreach ($premiumVisualSlides as $slideIndex => $slide)
                                    <figure class="premium-screen-media-slide {{ $slideIndex === 0 ? 'is-active' : '' }}"
                                            data-premium-visual-slide
                                            data-premium-visual-eyebrow="{{ $slide['eyebrow'] }}"
                                            data-premium-visual-title="{{ $slide['title'] }}"
                                            data-premium-visual-text="{{ $slide['text'] }}"
                                            aria-hidden="{{ $slideIndex === 0 ? 'false' : 'true' }}">
                                        <img src="{{ $slide['image'] }}"
                                             alt="{{ $slide['title'] }}"
                                             class="premium-screen-media-image"
                                             loading="{{ $slideIndex === 0 ? 'eager' : 'lazy' }}"
                                             decoding="async">
                                    </figure>
                                @endforeach
                            </div>
                        </div>

                    </div>
                </div>

                <div class="premium-float-grid" data-reveal-group data-reveal-step="100">
                    <article class="premium-float-card is-a">
                        <span>Estado operativo</span>
                        <strong>En línea</strong>
                        <p>La portada se siente más sólida, más deportiva y más atractiva sin tocar la lógica de negocio.</p>
                    </article>

                    <article class="premium-float-card is-b">
                        <span>Paleta activa</span>
                        <strong>Verde lima</strong>
                        <p>Oscuros profundos, verde lima y tipografía condensada inspirada en la referencia.</p>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="shell premium-section premium-feature-band">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Todo lo que necesitas para crecer</span>
        <h2>Todo lo que tu página principal necesita para vender con más fuerza.</h2>
        <p>Tomamos la energía visual del referente y la adaptamos para que FlexGym siga siendo software para gimnasios, no una plantilla genérica.</p>
    </header>

    <div class="premium-feature-grid">
        @foreach ($premiumBenefits as $benefit)
            <article class="premium-feature-card reveal {{ $loop->index === 1 ? 'is-accent' : '' }}">
                <div class="premium-feature-icon" aria-hidden="true">
                    @switch($benefit['icon'])
                        @case('speed')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 14.5 9 10.5l3 3L19 6.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M14.5 6.5H19v4.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('pulse')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 12h3.4l1.8-3.2 3.1 6.2 2.4-4.3H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('proof')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 3.8 18.5 6v5.6c0 3.5-2.1 6.7-6.5 8.6-4.4-1.9-6.5-5.1-6.5-8.6V6L12 3.8Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                <path d="m9.4 12.2 1.8 1.8 3.5-3.6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24" fill="none">
                                <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M12 8v4l2.7 1.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                    @endswitch
                </div>
                <h3>{{ $benefit['title'] }}</h3>
                <p>{{ $benefit['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="shell premium-section premium-programs">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Explora cada módulo clave</span>
        <h2>Explora los módulos del producto como si fueran piezas protagonistas de la marca.</h2>
        <p>Usamos miniaturas oscuras, superposiciones y llamados visuales más editoriales para que cada bloque se sienta intencional.</p>
    </header>

    <div class="premium-program-grid">
        @foreach ($premiumProgramCards as $card)
            <article class="premium-program-card reveal" style="--premium-program-image: url('{{ $card['image'] }}');">
                <div class="premium-program-shade"></div>
                <div class="premium-program-copy">
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['text'] }}</p>
                    <div class="premium-program-actions" data-reveal-group data-reveal-step="85">
                        <button type="button"
                                class="premium-program-modal-trigger"
                                data-interface-modal-open
                                data-modal-key="{{ $loop->index === 0 ? 'reception' : ($loop->index === 1 ? 'control' : ($loop->index === 2 ? 'mobile' : 'pricing')) }}"
                                data-modal-badge="{{ $card['modal_badge'] }}"
                                data-modal-title="{{ $card['modal_title'] }}"
                                data-modal-text="{{ $card['modal_text'] }}"
                                data-modal-image="{{ $card['modal_image'] !== '' ? $card['modal_image'] : $card['image'] }}"
                                data-modal-action-mode="{{ $loop->index === 3 ? 'link' : 'demo' }}"
                                data-modal-action-label="{{ $loop->index === 3 ? 'Ver precios y planes' : $premiumDemoLabel }}"
                                data-modal-action-href="{{ $loop->index === 3 ? '#pricing' : '' }}"
                                data-modal-note="{{ $loop->index === 3 ? 'Desde aqui bajas directo al comparador comercial de esta misma portada.' : 'Se abre un gimnasio temporal con datos de ejemplo para revisar este flujo sin afectar cuentas reales.' }}"
                                data-modal-items="{{ e(json_encode($card['modal_items'])) }}">
                            {{ $card['modal_button_label'] !== '' ? $card['modal_button_label'] : 'Ver detalle' }}
                        </button>
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>

<div id="interface-detail-backdrop" class="interface-modal-backdrop" hidden></div>
<div id="interface-detail-modal" class="interface-modal" hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="interface-modal-title">
    <div class="interface-modal-shell">
        <form id="interface-modal-demo-form" method="POST" action="{{ route('demo.request') }}" style="display:none;">
            @csrf
        </form>
        <button type="button" id="interface-detail-close" class="interface-modal-close" aria-label="Cerrar detalle">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M6 6l12 12M18 6 6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
            </svg>
        </button>

        <div class="interface-modal-copy">
            <span class="interface-modal-kicker" id="interface-modal-badge">Interfaz destacada</span>
            <h3 id="interface-modal-title">Detalle de la interfaz</h3>
            <p id="interface-modal-text">Explicación breve de la interfaz seleccionada.</p>

            <ul class="interface-modal-list" id="interface-modal-list"></ul>

            <div class="interface-modal-actions">
                <button id="interface-modal-primary-action" type="button" class="btn btn-demo">{{ $premiumDemoLabel }}</button>
                <button class="btn btn-wa btn-quote-trigger" hidden
                        type="button"
                        data-open-quote-modal
                        data-quote-source="interface_modal"
                        aria-controls="quote-request-modal">
                    <span class="quote-cta-icon-badge" aria-hidden="true">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                            <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>{{ $premiumQuoteLabel }}</span>
                </button>
            </div>
            <p class="interface-modal-note" id="interface-modal-note">Se abre un entorno temporal con datos de ejemplo para revisar el flujo sin afectar cuentas reales.</p>
        </div>

        <div class="interface-modal-visual">
            <div class="interface-modal-main-photo">
                <img id="interface-modal-image" src="" alt="">
            </div>
            <div class="interface-modal-secondary-photo" hidden>
                <img id="interface-modal-secondary-image" src="" alt="">
            </div>
        </div>
    </div>
</div>

<section class="shell premium-section premium-community">
    <div class="premium-community-panel reveal" style="--premium-community-image: url('{{ $premiumCommunityImage }}');">
        <div class="premium-community-overlay"></div>

        <div class="premium-community-copy">
            <span class="premium-kicker">Gimnasios que ya confían</span>
            <h2>Estas cifras reflejan la confianza de gimnasios que ya usan nuestro sistema para operar con más control.</h2>
            <p>Más sedes, más operaciones activas y una experiencia web y móvil pensada para gimnasios que quieren crecer con orden.</p>

            <div class="premium-community-stats" data-reveal-group data-reveal-step="90">
                @foreach ($premiumStatHighlights as $stat)
                    <article class="premium-community-stat">
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['label'] }}</span>
                    </article>
                @endforeach
            </div>

            <div class="premium-community-actions" data-reveal-group data-reveal-step="95">
                <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                    @csrf
                    <button class="btn btn-demo" type="submit">{{ $premiumDemoLabel }}</button>
                </form>

                <button class="btn btn-wa btn-quote-trigger"
                        type="button"
                        data-open-quote-modal
                        data-quote-source="community_panel"
                        aria-controls="quote-request-modal">
                    <span class="quote-cta-icon-badge" aria-hidden="true">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                            <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>{{ $premiumQuoteLabel }}</span>
                </button>
            </div>
        </div>
    </div>
</section>

<section class="shell premium-section premium-team">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Pensada para cada rol clave</span>
        <h2>Una portada pensada para recepción, administración, comercial y dirección.</h2>
        <p>La referencia visual se traduce en personajes y bloques humanos para que la página se sienta más viva y menos corporativa.</p>
    </header>

    <div class="premium-team-grid">
        @foreach ($premiumRoleCards as $card)
            <article class="premium-team-card reveal">
                <div class="premium-team-photo">
                    <img src="{{ $card['image'] }}" alt="{{ $card['role'] }}">
                </div>
                <div class="premium-team-content">
                    <span class="premium-team-role">{{ $card['role'] }}</span>
                    <p>{{ $card['text'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section id="pricing" class="shell premium-section premium-pricing">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Elige el plan ideal</span>
        <h2>Los planes ahora ocupan un lugar más protagonista y se entienden mejor al primer vistazo.</h2>
        <p>Mantenemos tus datos, tus botones y tu modal de cotización; solo cambiamos la puesta en escena para que vendan más.</p>
    </header>

    @if ($publicPlanCards->isNotEmpty())
        <div class="premium-plan-grid">
            @foreach ($publicPlanCards as $planCard)
                @php
                    $planKey = strtolower(trim((string) ($planCard['plan_key'] ?? '')));
                    $isFeatured = (bool) ($planCard['featured'] ?? false);
                    $isContactMode = (bool) ($planCard['contact_mode'] ?? false);
                    $price = (float) ($planCard['price'] ?? 0);
                    $discountPriceRaw = $planCard['discount_price'] ?? null;
                    $discountPrice = $discountPriceRaw !== null ? (float) $discountPriceRaw : null;
                    $offerText = trim((string) ($planCard['offer_text'] ?? ''));
                    $hasOffer = $offerText !== '' || ($discountPrice !== null && ($isContactMode || $discountPrice < $price));
                    $planFeatures = collect((array) ($planCard['features'] ?? []))
                        ->map(static fn ($feature): string => trim((string) $feature))
                        ->filter(static fn (string $feature): bool => $feature !== '')
                        ->take(4)
                        ->values();
                @endphp

                <article class="premium-plan-card reveal {{ $isFeatured ? 'is-featured' : '' }}">
                    <div class="premium-plan-head">
                        <div>
                            <span class="premium-plan-kicker">{{ $planSegments[$planKey] ?? 'Plan' }}</span>
                            <h3>{{ $planCard['name'] }}</h3>
                        </div>

                        @if ($isFeatured)
                            <span class="premium-plan-badge">Plan destacado</span>
                        @elseif ($hasOffer)
                            <span class="premium-plan-badge is-soft">Oferta</span>
                        @endif
                    </div>

                    <p class="premium-plan-summary">{{ $planCard['summary'] }}</p>

                    <div class="premium-plan-price">
                        @if ($isContactMode)
                            <strong>Personalizado</strong>
                            <span>según tu operación y tus sedes</span>
                        @else
                            <strong>${{ $formatPlanMoney($price) }}</strong>
                            <span>/mes</span>
                        @endif
                    </div>

                    @if ($hasOffer)
                        <div class="premium-plan-offer">
                            @if ($offerText !== '')
                                <strong>{{ $offerText }}</strong>
                            @elseif ($discountPrice !== null && $discountPrice < $price)
                                <span>Antes ${{ $formatPlanMoney($price) }}</span>
                                <strong>Ahora ${{ $formatPlanMoney($discountPrice) }}</strong>
                            @else
                                <strong>Oferta comercial activa</strong>
                            @endif
                        </div>
                    @endif

                    <ul class="premium-plan-features">
                        @foreach ($planFeatures as $feature)
                            <li>{{ preg_replace('/^incluido:\s*/iu', '', $feature) }}</li>
                        @endforeach
                    </ul>

                    <div class="premium-plan-actions">
                        @unless ($isContactMode)
                            <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                                @csrf
                                <button class="btn {{ $isFeatured ? 'btn-demo' : 'btn-outline' }}" type="submit">{{ $premiumDemoLabel }}</button>
                            </form>
                        @endunless

                        <button class="btn btn-quote-trigger {{ $isContactMode ? 'btn-demo' : 'btn-wa' }}"
                                type="button"
                                data-plan-cta-key="{{ $planKey }}"
                                data-open-quote-modal
                                data-quote-plan="{{ $planKey }}"
                                data-quote-plan-name="{{ trim((string) ($planCard['name'] ?? '')) }}"
                                data-quote-plan-summary="{{ trim((string) ($planCard['summary'] ?? '')) }}"
                                data-quote-plan-ideal="{{ trim((string) ($planCard['ideal_for'] ?? '')) }}"
                                data-quote-source="pricing_{{ $planKey }}"
                                aria-controls="quote-request-modal">
                            <span class="quote-cta-icon-badge" aria-hidden="true">
                                <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                                    <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                                    <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                            </span>
                            <span>Cotizar este plan</span>
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="premium-empty-state reveal">
            <h3>Los planes aparecerán aquí cuando estén listos.</h3>
            <p>Mientras tanto, puedes pedir una cotización y te ayudamos a definir la mejor configuración para tu gimnasio.</p>
            <button class="btn btn-demo btn-quote-trigger"
                    type="button"
                    data-open-quote-modal
                    data-quote-source="pricing_empty_state"
                    aria-controls="quote-request-modal">
                <span class="quote-cta-icon-badge" aria-hidden="true">
                    <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                        <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                        <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <span>{{ $premiumQuoteLabel }}</span>
            </button>
        </div>
    @endif
</section>

<section class="shell premium-section premium-reviews">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Resultados</span>
        <h2>Lo que gana la página principal con este rediseño.</h2>
        <p>No inventamos testimonios. Mostramos de forma honesta los cambios que elevan percepción, orden y conversión.</p>
    </header>

    <div class="premium-review-grid">
        @foreach ($premiumReviewCards as $card)
            <article class="premium-review-card reveal">
                <span class="premium-review-rating">★★★★★</span>
                <strong>{{ $card['eyebrow'] }}</strong>
                <p>{{ $card['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section id="faq" class="shell premium-section premium-faq">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Preguntas frecuentes</span>
        <h2>Respuestas claras antes de pedir una demo o una cotización.</h2>
        <p>La FAQ se mantiene funcional, pero ahora vive dentro del mismo lenguaje visual oscuro y deportivo.</p>
    </header>

    <div class="premium-faq-list">
        @foreach ($faqItems as $faqIndex => $faq)
            @php
                $faqAnchor = match ($faqIndex) {
                    0 => 'faq-demo',
                    1 => 'faq-sistema',
                    2 => 'faq-movil',
                    3 => 'faq-comercial',
                    default => 'faq-'.$faqIndex,
                };
            @endphp
            <article id="{{ $faqAnchor }}" class="premium-faq-item reveal {{ $faqIndex === 0 ? 'is-open' : '' }}" data-faq-item>
                <button class="premium-faq-button" type="button" data-faq-button aria-expanded="{{ $faqIndex === 0 ? 'true' : 'false' }}">
                    <span>{{ $faq['q'] }}</span>
                    <span class="premium-faq-plus" aria-hidden="true"></span>
                </button>
                <div class="premium-faq-content" data-faq-content>
                    <p>{{ $faq['a'] }}</p>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="shell premium-section premium-close">
    <div class="premium-close-panel reveal">
        <div class="premium-close-copy">
            <span class="premium-kicker">Último paso</span>
            <h2>Toda la portada fue reorientada para verse más sólida, más deportiva y más segura al momento de convertir.</h2>
            <p>La estructura completa ya conversa con la referencia que compartiste: negros profundos, verde lima, titulares más atléticos y una puesta en escena mucho más decidida, pero sin romper tus botones ni la lógica comercial.</p>

            <h2 class="premium-close-title-manual">
                <span>Toda la portada fue</span>
                <span>reorientada para verse</span>
                <span>m&aacute;s s&oacute;lida, m&aacute;s</span>
                <span>deportiva y m&aacute;s segura</span>
                <span>al momento de convertir.</span>
            </h2>

            <ul class="premium-close-list" data-reveal-group data-reveal-selector="li" data-reveal-step="80">
                <li>Demo gratis intacta.</li>
                <li>Cotización intacta.</li>
                <li>Inicio de sesión intacto.</li>
            </ul>
        </div>

        <div class="premium-close-card">
            <span class="premium-chip">Siguiente paso</span>
            <h3>Elige cómo quieres que arranque la conversación.</h3>
            <p>Puedes probar el sistema, cotizar el plan correcto o revisar precios primero. La experiencia está pensada para reducir fricción, no para esconder acciones.</p>

            <div class="premium-close-actions" data-reveal-group data-reveal-step="90">
                <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                    @csrf
                    <button class="btn btn-demo" type="submit">{{ $premiumDemoLabel }}</button>
                </form>

                <button class="btn btn-wa btn-quote-trigger"
                        type="button"
                        data-open-quote-modal
                        data-quote-source="close_section"
                        aria-controls="quote-request-modal">
                    <span class="quote-cta-icon-badge" aria-hidden="true">
                        <svg class="btn-icon" viewBox="0 0 24 24" fill="none">
                            <path d="M4 12.2 12.2 4H18a2 2 0 0 1 2 2v5.8L11.8 20 4 12.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <circle cx="16.25" cy="7.75" r="1.25" fill="currentColor"/>
                            <path d="M9.2 10.8h3.8M8.8 14.2h5.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                    <span>{{ $premiumQuoteLabel }}</span>
                </button>

                <a class="btn btn-ghost" href="{{ route('login') }}">{{ $loginLabel }}</a>
            </div>
        </div>
    </div>
</section>
