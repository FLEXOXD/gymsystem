@php
    $premiumDemoLabel = 'Probar demo gratis';
    $premiumQuoteLabel = 'Solicita tu cotizacion';
    $premiumHeroKicker = trim((string) ($content['hero_kicker'] ?? 'Software premium para gimnasios'));
    $premiumHeroTitle = trim((string) ($content['hero_title'] ?? ''));
    $premiumHeroTitle = $premiumHeroTitle !== ''
        ? $premiumHeroTitle
        : 'Controla cobros, acceso y operacion con una home de impacto real.';
    $premiumHeroSubtitle = trim((string) ($content['hero_subtitle'] ?? ''));
    $premiumHeroSubtitle = $premiumHeroSubtitle !== ''
        ? $premiumHeroSubtitle
        : 'Transformamos la pagina principal en una experiencia mas atletica, oscura y comercial, inspirada en la referencia visual compartida, pero aterrizada al software que vendes.';

    $premiumHeroPoints = [
        [
            'title' => 'Recepcion con mas energia',
            'text' => 'El mensaje principal ahora entra con fuerza y conecta mejor con gimnasios que buscan verse premium.',
        ],
        [
            'title' => 'CTAs mas visibles',
            'text' => 'Demo, cotizacion y login siguen vivos, solo mejor presentados y con mas jerarquia visual.',
        ],
        [
            'title' => 'Narrativa comercial clara',
            'text' => 'La home comunica producto, beneficios, modulos y planes sin sentirse como un SaaS generico.',
        ],
    ];

    $premiumBenefits = [
        [
            'icon' => 'speed',
            'title' => 'Experiencia Well Trainers',
            'text' => 'Recepcion, caja y acceso en un flujo mas directo para atender mejor desde el primer click.',
        ],
        [
            'icon' => 'pulse',
            'title' => 'Modern Equipments',
            'text' => 'Tus modulos clave se presentan como piezas premium del sistema: cobro, reportes, control y PWA.',
        ],
        [
            'icon' => 'proof',
            'title' => 'Proven Results',
            'text' => 'La propuesta comercial muestra orden, confianza y una percepcion de valor mucho mas alta.',
        ],
        [
            'icon' => 'clock',
            'title' => 'Flexible Honours',
            'text' => 'La estructura sigue siendo flexible para demo, cotizacion o login, sin perder la logica actual.',
        ],
    ];

    $capabilityModules = [
        [
            'title' => trim((string) ($content['section_1_title'] ?? 'Recepcion, caja y accesos')),
            'text' => trim((string) ($content['section_1_text'] ?? 'Cobras, validas acceso y mueves la operacion diaria desde una sola interfaz.')),
            'image' => trim((string) ($content['section_1_image_url'] ?? '')),
            'cta' => 'Ver flujo',
        ],
        [
            'title' => trim((string) ($content['section_2_title'] ?? 'Control comercial y reportes')),
            'text' => trim((string) ($content['section_2_text'] ?? 'Renovaciones, ingresos y seguimiento aparecen con un look mas editorial y entendible.')),
            'image' => trim((string) ($content['section_2_image_url'] ?? '')),
            'cta' => 'Explorar control',
        ],
        [
            'title' => trim((string) ($content['section_3_title'] ?? 'PWA y experiencia movil')),
            'text' => trim((string) ($content['section_3_text'] ?? 'El producto se siente mas moderno, listo para escritorio, tablet y movil.')),
            'image' => trim((string) ($content['section_3_image_url'] ?? '')),
            'cta' => 'Conocer PWA',
        ],
        [
            'title' => 'Planes y escalabilidad',
            'text' => 'La seccion de precios gana mas presencia para ayudar a elegir el plan correcto sin ruido.',
            'image' => trim((string) ($content['hero_slide_4_url'] ?? '')),
            'cta' => 'Ir a precios',
            'href' => '#pricing',
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
                'title' => trim((string) ($module['title'] ?? 'Modulo')),
                'text' => trim((string) ($module['text'] ?? '')),
                'image' => $image,
                'cta' => trim((string) ($module['cta'] ?? 'Ver mas')),
                'href' => $href !== '' ? $href : '#features',
            ];
        })
        ->values();

    $premiumVisualSlides = collect([
        [
            'eyebrow' => 'Recepcion activa',
            'nav' => 'Recepcion',
            'title' => 'Check-in, caja y acceso sin friccion.',
            'text' => 'La primera vista vende control diario con una escena mas viva y menos estatica.',
            'image' => $heroMediaSources[0] ?? $premiumHeroImage,
        ],
        [
            'eyebrow' => 'Cobros en marcha',
            'nav' => 'Cobros',
            'title' => 'Cobros y membresias bajo control.',
            'text' => 'Renovaciones, pagos y estado del gimnasio se entienden rapido desde la home.',
            'image' => $heroMediaSources[1] ?? $premiumCommunityImage,
        ],
        [
            'eyebrow' => 'Lectura operativa',
            'nav' => 'Reportes',
            'title' => 'Alertas y seguimiento listos para decidir.',
            'text' => 'El bloque visual comunica orden, control y valor real del sistema para vender mejor.',
            'image' => $heroMediaSources[2] ?? $premiumPromoFallback,
        ],
        [
            'eyebrow' => 'Experiencia movil',
            'nav' => 'PWA',
            'title' => 'Panel y PWA con energia premium.',
            'text' => 'La narrativa cambia sola para que no se sienta como una sola captura estatica.',
            'image' => $heroMediaSources[3] ?? $premiumPricingFallback,
        ],
    ])->values();

    $initialPremiumVisualSlide = $premiumVisualSlides->first();

    $premiumRoleCards = [
        [
            'role' => 'Recepcion',
            'text' => 'Un frente visualmente fuerte para mostrar check-in, caja y atencion en una sola experiencia.',
            'image' => trim((string) ($aboutTeamImage1 ?? '')) !== '' ? trim((string) $aboutTeamImage1) : $premiumHeroImage,
        ],
        [
            'role' => 'Administracion',
            'text' => 'Reportes, renovaciones y seguimiento con una presentacion mas premium y facil de leer.',
            'image' => trim((string) ($aboutTeamImage2 ?? '')) !== '' ? trim((string) $aboutTeamImage2) : $premiumCommunityImage,
        ],
        [
            'role' => 'Comercial',
            'text' => 'La home empuja mejor la cotizacion y hace que el producto se sienta mas vendible.',
            'image' => trim((string) ($aboutTeamImage3 ?? '')) !== '' ? trim((string) $aboutTeamImage3) : $premiumPromoFallback,
        ],
        [
            'role' => 'Direccion',
            'text' => 'Precios y cierre con una narrativa mas decidida para gimnasios que quieren crecer con orden.',
            'image' => trim((string) ($aboutStoryImage ?? '')) !== '' ? trim((string) $aboutStoryImage) : $premiumPricingFallback,
        ],
    ];

    $premiumReviewCards = [
        [
            'eyebrow' => 'Recepcion',
            'text' => 'La pagina principal ya no solo informa: ahora transmite velocidad, orden y una sensacion de marca mas fuerte.',
        ],
        [
            'eyebrow' => 'Operacion',
            'text' => 'Los modulos se entienden mas rapido y el usuario identifica en segundos donde esta el valor del sistema.',
        ],
        [
            'eyebrow' => 'Conversion',
            'text' => 'La cotizacion gana protagonismo sin romper la demo ni el login, que siguen trabajando igual.',
        ],
    ];

    $premiumStatHighlights = [
        [
            'value' => number_format((int) ($stats['gyms'] ?? 0)),
            'label' => 'GYMS',
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
            'label' => 'MOVIL',
        ],
    ];

    $planSegments = [
        'basico' => 'Base',
        'profesional' => 'Control',
        'premium' => 'Avanzado',
        'sucursales' => 'Multi-sede',
    ];
@endphp

<section id="inicio" class="shell premium-hero-section">
    <div class="premium-hero-layout">
        <div class="premium-hero-copy reveal">
            <div class="premium-hero-topline">
                <span class="premium-kicker">{{ $premiumHeroKicker }}</span>
                <span class="premium-chip">Ecuador y Latinoamerica</span>
            </div>

            <h1 class="premium-hero-title">{{ $premiumHeroTitle }}</h1>
            <p class="premium-hero-text">{{ $premiumHeroSubtitle }}</p>

            <div class="premium-hero-actions">
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

            <div class="premium-hero-points">
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

        <div class="premium-hero-visual reveal">
            <div class="premium-screen-shell">
                <div class="premium-screen-frame" data-premium-visual-carousel>
                    <div class="premium-screen-toolbar">
                        <span class="premium-screen-brand">{{ $brandName }}</span>
                        <span class="premium-screen-chip">FITNESS CONTROL</span>
                    </div>

                    <div class="premium-screen-canvas">
                        <div class="premium-screen-overlay"></div>

                        <div class="premium-screen-main">
                            <div class="premium-screen-copy">
                                <span data-premium-visual-eyebrow>{{ $initialPremiumVisualSlide['eyebrow'] ?? 'Control premium' }}</span>
                                <strong data-premium-visual-title>{{ $initialPremiumVisualSlide['title'] ?? 'Eleva tu gimnasio con una home mas poderosa.' }}</strong>
                                <p data-premium-visual-text>{{ $initialPremiumVisualSlide['text'] ?? 'Cobros, acceso, operacion y planes dentro de una narrativa visual mas intensa.' }}</p>
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

                        <div class="premium-screen-nav" aria-label="Vistas de Fitness Control">
                            @foreach ($premiumVisualSlides as $slideIndex => $slide)
                                <button type="button"
                                        class="premium-screen-nav-button {{ $slideIndex === 0 ? 'is-active' : '' }}"
                                        data-premium-visual-nav="{{ $slideIndex }}"
                                        aria-pressed="{{ $slideIndex === 0 ? 'true' : 'false' }}">
                                    <span>{{ str_pad((string) ($slideIndex + 1), 2, '0', STR_PAD_LEFT) }}</span>
                                    <strong>{{ $slide['nav'] }}</strong>
                                </button>
                            @endforeach
                        </div>

                        <div class="premium-screen-panel">
                            <article>
                                <span>Check-in</span>
                                <strong>97 hoy</strong>
                            </article>
                            <article>
                                <span>Cobros</span>
                                <strong>$1.480</strong>
                            </article>
                            <article>
                                <span>Alertas</span>
                                <strong>14 por revisar</strong>
                            </article>
                        </div>
                    </div>
                </div>

                <div class="premium-float-grid">
                    <article class="premium-float-card is-a">
                        <span>Estado operativo</span>
                        <strong>Online</strong>
                        <p>La home se siente mas fuerte, mas fitness y mas premium sin tocar la logica de negocio.</p>
                    </article>

                    <article class="premium-float-card is-b">
                        <span>Paleta activa</span>
                        <strong>Neon Lime</strong>
                        <p>Oscuros profundos, verde lima y tipografia condensada inspirada en la referencia.</p>
                    </article>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="features" class="shell premium-section premium-feature-band">
    <header class="premium-section-head reveal">
        <span class="premium-kicker">Everything you need to succeed</span>
        <h2>Todo lo que tu pagina principal necesita para vender con mas fuerza.</h2>
        <p>Tomamos la energia visual del referente y la adaptamos para que FlexGym siga siendo software para gimnasios, no una plantilla generica.</p>
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
        <span class="premium-kicker">Choose your perfect program</span>
        <h2>Explora los modulos del producto como si fueran piezas protagonistas de la marca.</h2>
        <p>Usamos miniaturas oscuras, overlays y llamadas visuales mas editoriales para que cada bloque se sienta intencional.</p>
    </header>

    <div class="premium-program-grid">
        @foreach ($premiumProgramCards as $card)
            <article class="premium-program-card reveal" style="--premium-program-image: url('{{ $card['image'] }}');">
                <div class="premium-program-shade"></div>
                <div class="premium-program-copy">
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['text'] }}</p>
                    <a href="{{ $card['href'] }}" class="premium-program-link">{{ $card['cta'] }}</a>
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="shell premium-section premium-community">
    <div class="premium-community-panel reveal" style="--premium-community-image: url('{{ $premiumCommunityImage }}');">
        <div class="premium-community-overlay"></div>

        <div class="premium-community-copy">
            <span class="premium-kicker">Join our strong digital community</span>
            <h2>La home ahora empuja demo, cotizacion y decision con una energia mucho mas clara.</h2>
            <p>Conservamos la funcionalidad existente, pero la envolvemos en un bloque mas dramatico, oscuro y orientado a conversion.</p>

            <div class="premium-community-stats">
                @foreach ($premiumStatHighlights as $stat)
                    <article class="premium-community-stat">
                        <strong>{{ $stat['value'] }}</strong>
                        <span>{{ $stat['label'] }}</span>
                    </article>
                @endforeach
            </div>

            <div class="premium-community-actions">
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
        <span class="premium-kicker">Meet our expert roles</span>
        <h2>Una home pensada para recepcion, administracion, comercial y direccion.</h2>
        <p>La referencia visual se traduce en personajes y bloques humanos para que la pagina se sienta mas viva y menos corporativa.</p>
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
        <span class="premium-kicker">Choose your perfect pricing plan</span>
        <h2>Los planes ahora ocupan un lugar mas protagonista y se entienden mejor al primer vistazo.</h2>
        <p>Mantenemos tus datos, tus botones y tu modal de cotizacion; solo cambiamos la puesta en escena para que vendan mas.</p>
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
                            <span class="premium-plan-badge">Top plan</span>
                        @elseif ($hasOffer)
                            <span class="premium-plan-badge is-soft">Oferta</span>
                        @endif
                    </div>

                    <p class="premium-plan-summary">{{ $planCard['summary'] }}</p>

                    <div class="premium-plan-price">
                        @if ($isContactMode)
                            <strong>Personalizado</strong>
                            <span>segun operacion y sedes</span>
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
            <h3>Los planes apareceran aqui cuando esten listos.</h3>
            <p>Mientras tanto, puedes pedir una cotizacion y te ayudamos a definir la mejor configuracion para tu gimnasio.</p>
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
        <span class="premium-kicker">Reviews</span>
        <h2>Lo que gana la pagina principal con este rediseño.</h2>
        <p>No inventamos testimonios. Mostramos de forma honesta los cambios que elevan percepcion, orden y conversion.</p>
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
        <h2>Respuestas claras antes de pedir demo o cotizacion.</h2>
        <p>La FAQ se mantiene funcional, pero ahora vive dentro del mismo lenguaje visual oscuro y deportivo.</p>
    </header>

    <div class="premium-faq-list">
        @foreach ($faqItems as $faqIndex => $faq)
            <article class="premium-faq-item reveal {{ $faqIndex === 0 ? 'is-open' : '' }}" data-faq-item>
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
            <span class="premium-kicker">Final call</span>
            <h2>Todo el home fue reorientado para verse mas fitness, mas premium y mas seguro al momento de convertir.</h2>
            <p>La estructura completa ya conversa con la referencia que compartiste: negros profundos, verde lima, titulares mas atleticos y una puesta en escena mucho mas agresiva, pero sin romper tus botones ni la logica comercial.</p>

            <ul class="premium-close-list">
                <li>Demo gratis intacta.</li>
                <li>Cotizacion intacta.</li>
                <li>Login intacto.</li>
            </ul>
        </div>

        <div class="premium-close-card">
            <span class="premium-chip">Siguiente paso</span>
            <h3>Elige como quieres que arranque la conversacion.</h3>
            <p>Puedes probar el sistema, cotizar el plan correcto o revisar precios primero. La experiencia esta pensada para reducir friccion, no para esconder acciones.</p>

            <div class="premium-close-actions">
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
