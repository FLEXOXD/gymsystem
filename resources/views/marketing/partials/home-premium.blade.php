@php
    $premiumDemoLabel = 'Probar demo gratis';
    $premiumQuoteLabel = 'Solicitar cotizacion';
    $premiumHeroKicker = trim((string) ($content['hero_kicker'] ?? 'Software premium para gimnasios'));
    $premiumHeroTitle = trim((string) ($content['hero_title'] ?? ''));
    $premiumHeroTitle = $premiumHeroTitle !== ''
        ? $premiumHeroTitle
        : 'Convierte la operacion de tu gimnasio en un sistema ordenado, rapido y listo para crecer.';
    $premiumHeroSubtitle = trim((string) ($content['hero_subtitle'] ?? ''));
    $premiumHeroSubtitle = $premiumHeroSubtitle !== ''
        ? $premiumHeroSubtitle
        : 'Controla cobros, membresias, asistencia, caja y reportes desde un software disenado para gimnasios que quieren verse y operar como un negocio serio.';

    $premiumHeroPoints = [
        [
            'title' => 'Control real del negocio',
            'text' => 'Cobros, vencimientos, caja y asistencia visibles desde una sola operacion.',
        ],
        [
            'title' => 'Automatizacion sin friccion',
            'text' => 'Tu equipo atiende mas rapido y comete menos errores en el dia a dia.',
        ],
        [
            'title' => 'Listo para crecer',
            'text' => 'Empieza con una sede y escala a planes mas completos o multi-sede sin migrar.',
        ],
    ];

    $premiumProofCards = [
        [
            'value' => number_format((int) ($stats['gyms'] ?? 0)),
            'label' => 'Gimnasios registrados',
            'text' => 'Operaciones que usan FlexGym para trabajar con mas orden.',
        ],
        [
            'value' => number_format((int) ($stats['active_sessions'] ?? 0)),
            'label' => 'Suscripciones activas',
            'text' => 'Negocios que hoy siguen operando sobre la plataforma.',
        ],
        [
            'value' => 'Web + PWA',
            'label' => 'Disponible en cualquier dispositivo',
            'text' => 'Recepcion y administracion en escritorio, tablet o movil.',
        ],
        [
            'value' => 'Acompanamiento',
            'label' => 'Implementacion guiada',
            'text' => 'Cotizacion, configuracion y puesta en marcha aterrizadas a tu contexto.',
        ],
    ];

    $premiumBenefits = [
        [
            'icon' => 'cash',
            'title' => 'Cobras mejor',
            'text' => 'Caja, membresias y pagos claros para reducir errores y fugas de ingreso.',
        ],
        [
            'icon' => 'calendar',
            'title' => 'Controlas vencimientos',
            'text' => 'Tienes a la vista renovaciones, pendientes y clientes por atender.',
        ],
        [
            'icon' => 'bolt',
            'title' => 'Atiendes mas rapido',
            'text' => 'Recepcion agil para cobrar, registrar y validar accesos sin enredos.',
        ],
        [
            'icon' => 'devices',
            'title' => 'Operas desde cualquier dispositivo',
            'text' => 'Trabaja desde escritorio, tablet o celular con una experiencia coherente.',
        ],
        [
            'icon' => 'growth',
            'title' => 'Tomas decisiones con datos',
            'text' => 'Reportes e indicadores para entender como se mueve tu operacion.',
        ],
        [
            'icon' => 'shield',
            'title' => 'Transmites mas confianza',
            'text' => 'Una operacion ordenada se siente mas profesional para clientes y equipo.',
        ],
    ];

    $capabilityModules = [
        [
            'key' => 'operacion',
            'eyebrow' => 'Operacion diaria',
            'title' => 'Recepcion, check-in y caja conectados en una sola vista',
            'text' => 'Todo lo esencial para atender rapido, cobrar con orden y dejar trazabilidad desde el primer dia.',
            'items' => [
                trim((string) ($content['section_1_item_1'] ?? 'Check-in con QR, RFID o validacion rapida')),
                trim((string) ($content['section_1_item_2'] ?? 'Cobros de membresia con control por metodo de pago')),
                trim((string) ($content['section_1_item_3'] ?? 'Caja por turnos y movimiento diario visible')),
            ],
            'image' => trim((string) ($content['section_1_image_url'] ?? '')),
        ],
        [
            'key' => 'control',
            'eyebrow' => 'Control comercial',
            'title' => 'Vencimientos, reportes y ventas para operar como un negocio que crece',
            'text' => 'La informacion importante deja de estar dispersa: ves ingresos, renovaciones, promociones y rendimiento del negocio.',
            'items' => [
                trim((string) ($content['section_2_item_1'] ?? 'Seguimiento de vencimientos y renovaciones')),
                trim((string) ($content['section_2_item_2'] ?? 'Reportes de ingresos, asistencias y membresias')),
                trim((string) ($content['section_2_item_3'] ?? 'Promociones, productos y control comercial')),
            ],
            'image' => trim((string) ($content['section_2_image_url'] ?? '')),
        ],
        [
            'key' => 'pwa',
            'eyebrow' => 'Experiencia movil',
            'title' => 'PWA lista para escritorio, tablet y celular',
            'text' => 'FlexGym se siente como producto serio: accesible, instalable y listo para acompanar la operacion donde ocurra.',
            'items' => [
                trim((string) ($content['section_3_item_1'] ?? 'Instalable como app para uso diario')),
                trim((string) ($content['section_3_item_2'] ?? 'Base preparada para recordatorios y notificaciones')),
                trim((string) ($content['section_3_item_3'] ?? 'Experiencia consistente para equipo y clientes')),
            ],
            'image' => trim((string) ($content['section_3_image_url'] ?? '')),
        ],
    ];

    $implementationSteps = [
        [
            'step' => '01',
            'title' => 'Entendemos tu operacion',
            'text' => 'Definimos si hoy necesitas una sola sede, mas control comercial o una operacion multi-sede.',
        ],
        [
            'step' => '02',
            'title' => 'Configuramos acceso, plan y flujo de trabajo',
            'text' => 'Dejas lista la recepcion, caja, membresias y usuarios para empezar sin ruido innecesario.',
        ],
        [
            'step' => '03',
            'title' => 'Tu equipo empieza a operar desde el dia uno',
            'text' => 'Cobros, check-in y control diario quedan centralizados para trabajar con mas velocidad.',
        ],
        [
            'step' => '04',
            'title' => 'Escalas sin volver al caos',
            'text' => 'Cuando creces, pasas a mas reportes, PWA o sucursales sin cambiar de sistema.',
        ],
    ];

    $comparisonRows = [
        [
            'label' => 'Sedes',
            'values' => [
                'basico' => '1 sede',
                'profesional' => '1 sede',
                'premium' => '1 sede',
                'sucursales' => 'Varias sedes',
            ],
        ],
        [
            'label' => 'Recepcion y caja',
            'values' => [
                'basico' => 'Incluido',
                'profesional' => 'Incluido',
                'premium' => 'Incluido',
                'sucursales' => 'Incluido',
            ],
        ],
        [
            'label' => 'Reportes exportables',
            'values' => [
                'basico' => 'Basico',
                'profesional' => 'CSV y PDF',
                'premium' => 'CSV y PDF',
                'sucursales' => 'Global por sede',
            ],
        ],
        [
            'label' => 'PWA instalable',
            'values' => [
                'basico' => 'No',
                'profesional' => 'Si',
                'premium' => 'Si',
                'sucursales' => 'Si',
            ],
        ],
        [
            'label' => 'App para clientes',
            'values' => [
                'basico' => 'No',
                'profesional' => 'No',
                'premium' => 'Incluida',
                'sucursales' => 'Incluida por sede',
            ],
        ],
        [
            'label' => 'Escalabilidad',
            'values' => [
                'basico' => 'Operacion base',
                'profesional' => 'Control reforzado',
                'premium' => 'Mayor automatizacion',
                'sucursales' => 'Gestion multi-sede',
            ],
        ],
    ];

    $confidenceCards = [
        [
            'title' => 'Prueba social honesta',
            'text' => 'Sin inventar testimonios: usamos indicadores reales, planes visibles y una propuesta comercial clara.',
        ],
        [
            'title' => 'Demo temporal y aislada',
            'text' => 'Puedes probar el sistema en un entorno de demostracion sin tocar tus datos reales.',
        ],
        [
            'title' => 'Aterrizado para Ecuador y Latinoamerica',
            'text' => 'La propuesta, el lenguaje comercial y la operacion estan pensados para gimnasios de la region.',
        ],
        [
            'title' => 'Acompanamiento comercial',
            'text' => 'No te dejamos solo con una interfaz bonita: te ayudamos a elegir la implementacion correcta.',
        ],
    ];

    $planSegments = [
        'basico' => 'Operacion base',
        'profesional' => 'Control reforzado',
        'premium' => 'Mas automatizacion',
        'sucursales' => 'Escala multi-sede',
    ];
@endphp

<section id="inicio" class="shell premium-hero-section">
    <div class="premium-hero-grid">
        <div class="premium-hero-copy reveal">
            <div class="premium-kicker-row">
                <span class="premium-eyebrow">{{ $premiumHeroKicker }}</span>
                <span class="premium-chip">Ecuador y Latinoamerica</span>
            </div>

            <h1 class="premium-hero-title">{{ $premiumHeroTitle }}</h1>
            <p class="premium-hero-text">{{ $premiumHeroSubtitle }}</p>

            <div class="premium-hero-actions">
                <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                    @csrf
                    <button class="btn btn-demo premium-btn-primary" type="submit">{{ $premiumDemoLabel }}</button>
                </form>

                <button class="btn btn-wa premium-btn-secondary"
                        type="button"
                        data-open-quote-modal
                        data-quote-source="hero_primary"
                        aria-controls="quote-request-modal">
                    {{ $premiumQuoteLabel }}
                </button>

                <a class="btn btn-ghost premium-btn-tertiary" href="#pricing">Ver planes</a>
            </div>

            <p class="premium-hero-microcopy">La demo crea un entorno temporal aislado para este dispositivo. Si ya eres cliente, <a href="{{ route('login') }}">{{ $loginLabel }}</a>.</p>

            <div class="premium-hero-points">
                @foreach ($premiumHeroPoints as $point)
                    <article class="premium-hero-point">
                        <span class="premium-hero-point-mark" aria-hidden="true"></span>
                        <div>
                            <h3>{{ $point['title'] }}</h3>
                            <p>{{ $point['text'] }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="premium-scene reveal" aria-hidden="true">
            <div class="premium-scene-glow premium-scene-glow--a"></div>
            <div class="premium-scene-glow premium-scene-glow--b"></div>

            <div class="premium-dashboard">
                <div class="premium-dashboard-toolbar">
                    <div class="premium-window-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="premium-toolbar-pill">Dashboard SaaS</div>
                    <div class="premium-toolbar-status">Online</div>
                </div>

                <div class="premium-dashboard-shell">
                    <aside class="premium-dashboard-sidebar">
                        <div class="premium-sidebar-badge">{{ $brandInitials }}</div>
                        <span class="premium-sidebar-item is-active">Resumen</span>
                        <span class="premium-sidebar-item">Recepcion</span>
                        <span class="premium-sidebar-item">Caja</span>
                        <span class="premium-sidebar-item">Clientes</span>
                        <span class="premium-sidebar-item">Reportes</span>
                    </aside>

                    <div class="premium-dashboard-body">
                        <article class="premium-screen-card premium-screen-card--wide">
                            <div class="premium-card-head">
                                <div>
                                    <span class="premium-card-label">Cobro del dia</span>
                                    <strong class="premium-card-figure">$1.480</strong>
                                </div>
                                <span class="premium-card-trend is-up">+18% esta semana</span>
                            </div>
                            <div class="premium-chart-bars">
                                <span style="height: 34%"></span>
                                <span style="height: 58%"></span>
                                <span style="height: 52%"></span>
                                <span style="height: 76%"></span>
                                <span style="height: 68%"></span>
                                <span style="height: 92%"></span>
                                <span style="height: 84%"></span>
                            </div>
                        </article>

                        <article class="premium-screen-card">
                            <span class="premium-card-label">Vencimientos por atender</span>
                            <ul class="premium-task-list">
                                <li><span>Hoy</span><strong>14 membresias</strong></li>
                                <li><span>En riesgo</span><strong>6 por renovar</strong></li>
                                <li><span>Seguimiento</span><strong>3 promociones activas</strong></li>
                            </ul>
                        </article>

                        <article class="premium-screen-card">
                            <span class="premium-card-label">Recepcion en tiempo real</span>
                            <div class="premium-checkin-grid">
                                <article>
                                    <strong>97</strong>
                                    <span>Check-ins</span>
                                </article>
                                <article>
                                    <strong>11</strong>
                                    <span>Por validar</span>
                                </article>
                                <article>
                                    <strong>4</strong>
                                    <span>Cajas abiertas</span>
                                </article>
                            </div>
                        </article>

                        <article class="premium-screen-card premium-screen-card--mobile">
                            <span class="premium-card-label">Experiencia movil / PWA</span>
                            <div class="premium-phone-preview">
                                <div class="premium-phone-bar"></div>
                                <div class="premium-phone-ticket">
                                    <span>QR dinamico</span>
                                    <strong>Ingreso habilitado</strong>
                                </div>
                                <div class="premium-phone-notice">Recordatorio de renovacion listo para enviar</div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>

            <article class="premium-float-card premium-float-card--payments">
                <span>Pagos conciliados</span>
                <strong>89%</strong>
                <p>Control diario visible para recepcion y administracion.</p>
            </article>

            <article class="premium-float-card premium-float-card--attendance">
                <span>Asistencia actual</span>
                <strong>En linea</strong>
                <p>Validacion rapida desde escritorio, tablet o movil.</p>
            </article>

            <article class="premium-float-card premium-float-card--clients">
                <span>Clientes por recuperar</span>
                <strong>24</strong>
                <p>Renovaciones y seguimiento con mejor contexto comercial.</p>
            </article>
        </div>
    </div>
</section>

<section class="shell premium-proof-strip">
    <div class="premium-proof-grid">
        @foreach ($premiumProofCards as $card)
            <article class="premium-proof-card reveal">
                <strong>{{ $card['value'] }}</strong>
                <span>{{ $card['label'] }}</span>
                <p>{{ $card['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section class="shell premium-section premium-benefits">
    <header class="premium-section-head reveal">
        <span class="premium-eyebrow">Resultados visibles</span>
        <h2>Menos caos operativo. Mas control, velocidad y confianza para vender mejor.</h2>
        <p>La home ahora comunica beneficios reales para el negocio, con menos ruido y una jerarquia visual mucho mas clara.</p>
    </header>

    <div class="premium-benefits-grid">
        @foreach ($premiumBenefits as $benefit)
            <article class="premium-benefit-card reveal">
                <div class="premium-benefit-icon" aria-hidden="true">
                    @switch($benefit['icon'])
                        @case('cash')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M4 7.5c0-1.38 1.12-2.5 2.5-2.5h11c1.38 0 2.5 1.12 2.5 2.5v9c0 1.38-1.12 2.5-2.5 2.5h-11A2.5 2.5 0 0 1 4 16.5v-9Z" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8.5 12h7M12 9.5v5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('calendar')
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="4" y="5" width="16" height="15" rx="3" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M8 3.5v3M16 3.5v3M4 9.5h16" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                                <path d="M8.5 13h7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('bolt')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M13.5 3 6 13.2h4.7L9.8 21 18 10.8h-4.7L13.5 3Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @case('devices')
                            <svg viewBox="0 0 24 24" fill="none">
                                <rect x="3" y="6" width="11" height="9" rx="2.4" stroke="currentColor" stroke-width="1.7"/>
                                <rect x="16.5" y="4" width="4.5" height="14" rx="1.8" stroke="currentColor" stroke-width="1.7"/>
                                <path d="M7 18.5h3" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                            @break
                        @case('growth')
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M5 17.5 10 12.5l3.5 3.5L19 9.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M15.5 9.5H19v3.5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            @break
                        @default
                            <svg viewBox="0 0 24 24" fill="none">
                                <path d="M12 4.5 5 8.5v7l7 4 7-4v-7l-7-4Z" stroke="currentColor" stroke-width="1.7" stroke-linejoin="round"/>
                                <path d="M9 12h6" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/>
                            </svg>
                    @endswitch
                </div>
                <h3>{{ $benefit['title'] }}</h3>
                <p>{{ $benefit['text'] }}</p>
            </article>
        @endforeach
    </div>
</section>

<section id="features" class="shell premium-section premium-capabilities">
    <header class="premium-section-head reveal">
        <span class="premium-eyebrow">Capacidades del producto</span>
        <h2>Una arquitectura pensada para operar bien hoy y crecer sin improvisar manana.</h2>
        <p>Dividimos la propuesta en capas claras para que el valor se entienda rapido: operacion, control comercial y experiencia movil.</p>
    </header>

    <div class="premium-module-stack">
        @foreach ($capabilityModules as $module)
            <article class="premium-module premium-module--{{ $module['key'] }} reveal">
                <div class="premium-module-copy">
                    <span class="premium-chip">{{ $module['eyebrow'] }}</span>
                    <h3>{{ $module['title'] }}</h3>
                    <p>{{ $module['text'] }}</p>

                    <ul class="premium-module-list">
                        @foreach ($module['items'] as $item)
                            @if ($item !== '')
                                <li>{{ $item }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>

                <div class="premium-module-visual" aria-hidden="true">
                    @if ($module['key'] === 'operacion')
                        <div class="premium-visual-console">
                            <div class="premium-visual-line"><span>Ingreso</span><strong>QR validado</strong></div>
                            <div class="premium-visual-line"><span>Caja</span><strong>$420 recibidos</strong></div>
                            <div class="premium-visual-line"><span>Recepcion</span><strong>3 clientes en espera</strong></div>
                            <div class="premium-visual-badge">Turno activo</div>
                        </div>
                    @elseif ($module['key'] === 'control')
                        <div class="premium-visual-metrics">
                            <article><span>Vencen hoy</span><strong>14</strong></article>
                            <article><span>Ingresos</span><strong>$2.180</strong></article>
                            <article><span>Renovacion</span><strong>74%</strong></article>
                            <div class="premium-visual-bars">
                                <span style="height: 30%"></span>
                                <span style="height: 48%"></span>
                                <span style="height: 68%"></span>
                                <span style="height: 88%"></span>
                            </div>
                        </div>
                    @else
                        <div class="premium-visual-phone">
                            <div class="premium-visual-phone-notch"></div>
                            <div class="premium-visual-phone-card">
                                <span>App / PWA</span>
                                <strong>QR dinamico listo</strong>
                            </div>
                            <div class="premium-visual-phone-note">Recordatorio de pago programado</div>
                        </div>
                    @endif

                    @if ($module['image'] !== '')
                        <div class="premium-module-photo">
                            <img src="{{ $module['image'] }}" alt="{{ $module['title'] }}">
                        </div>
                    @endif
                </div>
            </article>
        @endforeach
    </div>
</section>

<section class="shell premium-section premium-journey">
    <header class="premium-section-head reveal">
        <span class="premium-eyebrow">Como funciona</span>
        <h2>Una implementacion clara, visual y facil de entender para tu equipo.</h2>
        <p>La idea es que el usuario entienda en segundos como pasa de interes comercial a operacion ordenada.</p>
    </header>

    <ol class="premium-timeline">
        @foreach ($implementationSteps as $step)
            <li class="premium-step reveal">
                <span class="premium-step-count">{{ $step['step'] }}</span>
                <div class="premium-step-card">
                    <h3>{{ $step['title'] }}</h3>
                    <p>{{ $step['text'] }}</p>
                </div>
            </li>
        @endforeach
    </ol>
</section>

<section id="pricing" class="shell premium-section premium-pricing">
    <header class="premium-section-head reveal">
        <span class="premium-eyebrow">Planes listos para vender</span>
        <h2>Elige el nivel de control que necesita hoy tu gimnasio y escala sin cambiar de sistema.</h2>
        <p>Reordenamos la informacion para que cada plan se entienda mejor, destaque lo importante y reduzca saturacion visual.</p>
    </header>

    <div class="premium-pricing-highlight reveal">
        <article>
            <strong>Comparacion mas limpia</strong>
            <span>Primero ves para quien es cada plan y despues revisas detalles clave.</span>
        </article>
        <article>
            <strong>CTA mas vendible</strong>
            <span>Activas demo rapido o cotizas el plan correcto segun tu operacion.</span>
        </article>
        <article>
            <strong>Escalabilidad clara</strong>
            <span>Desde una sede hasta varias sucursales, la progresion se entiende mejor.</span>
        </article>
    </div>

    <div class="premium-pricing-grid">
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
                $planFeatures = array_values(array_filter((array) ($planCard['features'] ?? []), fn ($item) => is_string($item) && trim($item) !== ''));
                $highlightedFeatures = collect($planFeatures)
                    ->filter(static fn (string $feature): bool => str_starts_with(mb_strtolower(trim($feature)), 'incluido:'))
                    ->map(static fn (string $feature): string => trim((string) preg_replace('/^incluido:\s*/iu', '', $feature)))
                    ->values()
                    ->all();
                $visibleFeatures = collect($planFeatures)
                    ->reject(static fn (string $feature): bool => str_starts_with(mb_strtolower(trim($feature)), 'incluido:'))
                    ->reject(static fn (string $feature): bool => str_starts_with(mb_strtolower(trim($feature)), 'todo lo del'))
                    ->take(4)
                    ->values()
                    ->all();
                $hiddenFeatureCount = max(0, count($planFeatures) - count($highlightedFeatures) - count($visibleFeatures));
            @endphp
            <article class="premium-plan-card reveal {{ $isFeatured ? 'is-featured' : '' }} {{ $isContactMode ? 'is-contact' : '' }}">
                <div class="premium-plan-top">
                    <div>
                        <span class="premium-plan-kicker">{{ $planSegments[$planKey] ?? 'Plan FlexGym' }}</span>
                        <h3>{{ $planCard['name'] }}</h3>
                    </div>

                    @if ($isFeatured)
                        <span class="premium-plan-badge">Recomendado</span>
                    @elseif ($hasOffer)
                        <span class="premium-plan-badge is-soft">Oferta</span>
                    @endif
                </div>

                <div class="premium-plan-price">
                    @if ($isContactMode)
                        <strong>Personalizado</strong>
                        <span>segun cantidad de sedes y nivel de operacion</span>
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
                            <strong>Oferta comercial disponible</strong>
                        @endif
                    </div>
                @endif

                <p class="premium-plan-summary">{{ $planCard['summary'] }}</p>
                <div class="premium-plan-meta">{{ $planCard['ideal_for'] }}</div>

                <ul class="premium-plan-features">
                    @foreach ($visibleFeatures as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach

                    @foreach ($highlightedFeatures as $feature)
                        <li class="is-highlight">{{ $feature }}</li>
                    @endforeach
                </ul>

                @if ($hiddenFeatureCount > 0)
                    <p class="premium-plan-more">+{{ $hiddenFeatureCount }} capacidades adicionales segun este plan.</p>
                @endif

                <div class="premium-plan-actions">
                    @unless ($isContactMode)
                        <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                            @csrf
                            <button class="btn {{ $isFeatured ? 'btn-demo' : 'btn-outline' }}" type="submit">{{ $premiumDemoLabel }}</button>
                        </form>
                    @endunless

                    <button class="btn {{ $isContactMode ? 'btn-demo' : 'btn-wa' }}"
                            type="button"
                            data-plan-cta-key="{{ $planKey }}"
                            data-open-quote-modal
                            data-quote-plan="{{ $planKey }}"
                            data-quote-source="pricing_{{ $planKey }}"
                            aria-controls="quote-request-modal">
                        {{ $isContactMode ? 'Solicitar cotizacion' : 'Cotizar este plan' }}
                    </button>
                </div>

                <p class="premium-plan-note">{{ $planCard['setup_note'] }}</p>
            </article>
        @endforeach
    </div>

    <div class="premium-comparison reveal">
        <div class="premium-comparison-head">
            <div>
                <span class="premium-eyebrow">Comparacion rapida</span>
                <h3>Lo esencial, lado a lado.</h3>
            </div>
            <p>Una lectura limpia para evaluar sedes, PWA, app cliente y nivel de control.</p>
        </div>

        <div class="premium-comparison-wrap">
            <table class="premium-comparison-table">
                <thead>
                    <tr>
                        <th>Capacidad</th>
                        @foreach ($publicPlanCards as $planCard)
                            <th>{{ $planCard['name'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comparisonRows as $row)
                        <tr>
                            <th>{{ $row['label'] }}</th>
                            @foreach ($publicPlanCards as $planCard)
                                @php
                                    $planKey = strtolower(trim((string) ($planCard['plan_key'] ?? '')));
                                    $cellValue = $row['values'][$planKey] ?? 'Segun plan';
                                @endphp
                                <td>{{ $cellValue }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>

<section class="shell premium-section premium-confidence">
    <header class="premium-section-head reveal">
        <span class="premium-eyebrow">Confianza para decidir</span>
        <h2>Una propuesta comercial honesta que transmite seguridad sin inventar historias.</h2>
        <p>Usamos indicadores reales, beneficios concretos y promesas operativas creibles para que FlexGym se sienta confiable y profesional.</p>
    </header>

    <div class="premium-confidence-grid">
        <article class="premium-confidence-card premium-confidence-card--lead reveal">
            <span class="premium-chip">Negocio que crece</span>
            <h3>FlexGym se presenta como software de control y crecimiento, no como pagina informativa generica.</h3>
            <p>El enfoque ahora pone primero lo que el cliente necesita entender rapido: que resuelve, para quien es, por que se ve serio y cual boton debe tocar.</p>

            <ul class="premium-confidence-list">
                <li>Jerarquia visual fuerte para captar atencion y guiar a la accion.</li>
                <li>Copy mas corto y orientado a resultado real para gimnasios.</li>
                <li>Escenas SaaS y componentes visuales que elevan la percepcion de valor.</li>
            </ul>
        </article>

        <div class="premium-confidence-side">
            @foreach ($confidenceCards as $card)
                <article class="premium-confidence-card reveal">
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['text'] }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section id="faq" class="shell premium-section premium-faq">
    <header class="premium-section-head reveal">
        <span class="premium-eyebrow">Preguntas frecuentes</span>
        <h2>Respuestas claras para avanzar sin dudas.</h2>
        <p>Un acordeon mas limpio, mas premium y mejor integrado con el resto de la experiencia.</p>
    </header>

    <div class="premium-faq-list">
        @foreach ($faqItems as $faqIndex => $faq)
            <article class="premium-faq-item reveal {{ $faqIndex === 0 ? 'is-open' : '' }}" data-faq-item>
                <button class="premium-faq-button" type="button" data-faq-button aria-expanded="{{ $faqIndex === 0 ? 'true' : 'false' }}">
                    <span class="premium-faq-question">{{ $faq['q'] }}</span>
                    <span class="premium-faq-icon" aria-hidden="true"></span>
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
            <span class="premium-eyebrow">Cotizacion guiada</span>
            <h2>Recibe una propuesta clara para tu gimnasio, con foco en tu operacion real.</h2>
            <p>Cu&eacute;ntanos cuantas personas trabajan contigo, si manejas una o varias sedes y que nivel de control necesitas. Te ayudamos a elegir el plan correcto y la ruta de implementacion.</p>

            <ul class="premium-close-list">
                <li>Microcopy orientado a conversion y menos friccion visual.</li>
                <li>Proceso comercial claro para demo, cotizacion y eleccion de plan.</li>
                <li>Soporte pensado para gimnasios que quieren crecer con orden.</li>
            </ul>
        </div>

        <div class="premium-close-card">
            <span class="premium-chip">Que pasa despues</span>

            <div class="premium-close-steps">
                <article>
                    <strong>1.</strong>
                    <span>Revisamos tu contexto y el plan ideal para tu operacion.</span>
                </article>
                <article>
                    <strong>2.</strong>
                    <span>Te contactamos por correo o telefono con una propuesta clara.</span>
                </article>
                <article>
                    <strong>3.</strong>
                    <span>Si avanzas, configuramos el siguiente paso con enfoque comercial y operativo.</span>
                </article>
            </div>

            <div class="premium-close-actions">
                <form class="inline-form" method="POST" action="{{ route('demo.request') }}">
                    @csrf
                    <button class="btn btn-demo" type="submit">{{ $premiumDemoLabel }}</button>
                </form>

                <button class="btn btn-wa"
                        type="button"
                        data-open-quote-modal
                        data-quote-source="close_section"
                        aria-controls="quote-request-modal">
                    {{ $premiumQuoteLabel }}
                </button>

                <a class="btn btn-ghost" href="#pricing">Ver planes</a>
            </div>

            <p class="premium-close-footnote">Si prefieres una conversacion directa, tambi&eacute;n puedes ir a <a href="{{ $contactHref }}">Cont&aacute;ctanos</a>.</p>
        </div>
    </div>
</section>
